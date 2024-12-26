<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\ResponseResult;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Throwable;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $offset = request()->query('offset', 0);
            $customers = Customer::where('shop_id', auth()->id())->orderByDesc('updated_at')
                ->skip($offset)
                ->take(10)->get();
            return ResponseResult::Success($customers);
        } catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        try{
            $customer = Customer::create(array_merge($request->validated(), ['shop_id' => auth()->id()]));
            return ResponseResult::Success($customer);
        } catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        try{
            AuthService::checkUserAccess($customer->shop_id);
            return ResponseResult::Success($customer);
        } catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        try{
            AuthService::checkUserAccess($customer->shop_id);
            $customer->update($request->validated());
            return ResponseResult::Success($customer);
        } catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try{
            AuthService::checkUserAccess($customer->shop_id);
            $customer->delete();
            return ResponseResult::Success(null);
        } catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }
}
