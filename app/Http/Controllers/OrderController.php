<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\ErrorMessages;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ResponseResult;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $offset = request()->query('offset', 0);
            $orders = Order::where('shop_id', auth()->id())->orderByDesc('updated_at')
                ->skip($offset)
                ->take(10)->get();
            return ResponseResult::Success(OrderResource::collection($orders));
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        try{
            $user = auth()->user();
            $productDetails = $request->get('products', []); // Array of product IDs with quantities, e.g., [1 => 2, 2 => 3]

            DB::beginTransaction();
            $order = Order::create(array_merge($request->validated(), [
                'shop_id' => auth()->id(),
            ]));

            // Prepare pivot table data with prices and quantities
            $syncData = [];
            foreach ($productDetails as $productId => $quantity) {
                $product = Product::find($productId);

                // Validate
                if ($product->shop_id != $order->shop_id) {
                    throw new \Exception(ErrorMessages::getMessage($user->language, 'unauthorized'));
                }

                $syncData[$productId] = [
                    'price_on_created' => $product->price,
                    'purchase_price_on_created' => $product->purchase_price,
                    'quantity' => $quantity,
                ];
            }
            // Attach products to the order with additional pivot fields
            $order->products()->attach($syncData);
            $paidAmount = $request->get('paid');
            if ($paidAmount) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $paidAmount,
                ]);
            }
            DB::commit();
            return ResponseResult::Success(OrderResource::collection($order));
        }catch (\Exception $e){
            DB::rollBack();
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        try{
            AuthService::checkUserAccess($order->shop_id);
            return ResponseResult::Success(new OrderResource($order));
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOrderRequest $request, Order $order)
    {
        try{
            DB::beginTransaction();
            AuthService::checkUserAccess($order->shop_id);
            $language = auth()->user()->language;

            if ($order->isLocked) {
                throw new \Exception(ErrorMessages::getMessage($language, 'locked_order'));
            }
            $order->update($request->validated());

            // Get product details from the request
            $productDetails = $request->get('products', []); // Array of product IDs with quantities, e.g., [1 => 2, 2 => 3]

            $syncData = [];
            foreach ($productDetails as $productId => $quantity) {
                $product = Product::find($productId);

                // Validate
                if ($product->shop_id != $order->shop_id) {
                    throw new \Exception(ErrorMessages::getMessage($language, 'unauthorized'));
                }

                // Prepare sync data with additional pivot fields
                $syncData[$productId] = [
                    'price_on_created' => $product->price,
                    'purchase_price_on_created' => $product->purchase_price,
                    'quantity' => $quantity,
                ];
            }
            $order->products()->sync($syncData);
            $paidAmount = $request->get('paid');
            if ($paidAmount) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $paidAmount,
                ]);
            }
            DB::commit();
            return ResponseResult::Success(new OrderResource($order));
        }catch (\Exception $e){
            DB::rollBack();
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try{
            AuthService::checkUserAccess($order->shop_id);
            $order->delete();
            return ResponseResult::Success(null);
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }
}
