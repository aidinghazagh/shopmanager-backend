<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\ErrorMessages;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ResponseResult;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Order $order)
    {
        try {
            if($order->shop_id != auth()->id()){
                throw new Exception(ErrorMessages::getMessage(auth()->user()->language, 'unauthorized'));
            }
            return ResponseResult::Success($order->payments()->orderBy('created_at', 'desc')->get());
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Order $order, StorePaymentRequest $request)
    {
        try {
            $payment = Payment::create(array_merge($request->validated(), ['order_id' => $order->id]));
            return ResponseResult::Success($payment);
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        try {
            if($payment->order->shop_id != auth()->id()){
                throw new Exception(ErrorMessages::getMessage(auth()->user()->language, 'unauthorized'));
            }
            return ResponseResult::Success($payment);
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
//    public function update(StorePaymentRequest $request, Payment $payment)
//    {
//        try {
//            if($payment->order->shop_id != auth()->id()){
//                throw new Exception(ErrorMessages::getMessage(auth()->user()->language, 'unauthorized'));
//            }
//            if($payment->order_id != $request->order_id){
//                throw new Exception(ErrorMessages::getMessage(auth()->user()->language, 'order_id_not_changeable'));
//            }
//            $payment->update($request->validated());
//            return ResponseResult::Success($payment);
//        }catch (\Exception $e){
//            return ResponseResult::Failure([$e->getMessage()]);
//        }
//    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            if($payment->order->shop_id != auth()->id()){
                throw new Exception(ErrorMessages::getMessage(auth()->user()->language, 'unauthorized'));
            }
            $payment->delete();
            return ResponseResult::Success(null);
        }catch (\Exception $e){
            return ResponseResult::Failure([$e->getMessage()]);
        }
    }
}
