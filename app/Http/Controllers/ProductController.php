<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductInventoryLog;
use App\Models\ProductLog;
use App\Models\ResponseResult;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $offset = request()->query('offset', 0);
            $products = Product::where('shop_id', auth()->id())->orderByDesc('updated_at')
                ->skip($offset)
                ->take(10)->get();
            return ResponseResult::success(ProductResource::collection($products));
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }
    public function dropdown()
    {
        try{
            $products = Product::select('name', 'id', 'price')->where('shop_id', auth()->id())->orderByDesc('updated_at')->get();
            return ResponseResult::success($products);
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try{
            DB::beginTransaction();
            $product = Product::create(array_merge(
                $request->validated(),
                ['shop_id' => auth()->id()]
            ));
            ProductInventoryLog::create([
                'product_id' => $product->id,
                'quantity_change' => $request->inventory,
            ]);
            DB::commit();
            return ResponseResult::Success(ProductResource::make($product));
        }catch (\Exception $e){
            DB::rollBack();
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try{
            AuthService::checkUserAccess($product->shop_id);
            return ResponseResult::Success(ProductResource::make($product));
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        try {
            AuthService::checkUserAccess($product->shop_id);
            DB::beginTransaction();
            $originalProduct = $product->getOriginal(); // Fetch the original product data
            $updatedData = $request->validated(); // Data being updated

            $changedFields = [];
            foreach ($updatedData as $field => $newValue) {
                if (array_key_exists($field, $originalProduct) && $originalProduct[$field] != $newValue) {
                    $changedFields[] = [
                        'product_id' => $product->id,
                        'changed_field' => $field,
                        'old_value' => $originalProduct[$field],
                        'new_value' => $newValue,
                    ];
                }
            }

            foreach ($changedFields as $changedField) {
                ProductLog::create($changedField);
            }
            $currentInventory = $product->inventory;
            $newInventory = $request->input('inventory');
            $inventoryChange = $newInventory - $currentInventory;
            if ($inventoryChange !== 0) {
                ProductInventoryLog::create([
                    'product_id' => $product->id,
                    'quantity_change' => $inventoryChange,
                ]);
            }

            $product->update($updatedData);
            DB::commit();
            return ResponseResult::Success(ProductResource::make($product));
        }catch (\Exception $e){
            DB::rollBack();
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            AuthService::checkUserAccess($product->shop_id);
            $product->delete();
            return ResponseResult::Success(null);
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }
}
