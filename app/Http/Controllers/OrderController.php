<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ResponseResult;
use App\Services\AuthService;
use Illuminate\Http\Request;

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

        }catch (\Exception $e){
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
            AuthService::checkUserAccess($order->shop_id);
            return ResponseResult::Success(new OrderResource($order));
        }catch (\Exception $e){
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
