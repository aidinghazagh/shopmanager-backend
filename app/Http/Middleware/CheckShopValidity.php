<?php

namespace App\Http\Middleware;

use App\Models\ErrorMessages;
use App\Models\ResponseResult;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckShopValidity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shop = auth()->user();
        if($shop->valid_until < now()){
            return ResponseResult::Failure([ErrorMessages::getMessage($shop->language, 'shop_expired')]);
        }
        return $next($request);
    }
}
