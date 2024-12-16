<?php

namespace App\Http\Middleware;

use App\Models\ErrorMessages;
use App\Models\ResponseResult;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            $default_lang = $request->default_lang ?? null;
            return ResponseResult::Failure([ErrorMessages::getMessage($default_lang, 'unauthenticated')]);
        }

        return $next($request);
    }
}
