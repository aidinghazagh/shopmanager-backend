<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\CheckIfAuthenticated;
use App\Http\Middleware\CheckShopValidity;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware([CheckIfAuthenticated::class])->group(function () {
    // Protected routes that require authentication
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('shop', [ShopController::class, 'index']);
    Route::patch('shop/{shop}', [ShopController::class, 'update']);

    Route::middleware(CheckShopValidity::class)->group(function () {

        Route::get('product/dropdown', [ProductController::class, 'dropdown']);
        Route::get('customer/dropdown', [CustomerController::class, 'dropdown']);
        // Routes that needs the active shop subscription
        Route::resource('product', ProductController::class);
        Route::get('customer/{customer}/orders', [CustomerController::class, 'orders']);
        Route::resource('customer', CustomerController::class);
        Route::delete('payment/{payment}', [PaymentController::class, 'destroy']);
        Route::get('order/{order}/payment', [PaymentController::class, 'index']);
        Route::post('order/{order}/payment', [PaymentController::class, 'store']);
        Route::resource('order', OrderController::class);
    });
});
