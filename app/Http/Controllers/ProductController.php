<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ResponseResult;
use App\Services\AuthService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $offset = request()->query('offset', 0);
            $products = Product::select('id', 'name', 'price')->where('shop_id', auth()->id())->orderByDesc('updated_at')
                ->skip($offset)
                ->take(10)->get();
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
            $product = Product::create(array_merge(
                $request->validated(),
                ['shop_id' => auth()->id()]
            ));
            return ResponseResult::Success($product);
        }catch (\Exception $e){
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
            return ResponseResult::Success($product);
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
            $product->update($request->validated());
            return ResponseResult::Success($product);
        }catch (\Exception $e){
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
