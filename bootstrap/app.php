<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\ResponseResult;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, \Illuminate\Http\Request $request) {
            if($request->wantsJson()){
                return ResponseResult::ValidationError((object)$e->errors());
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            if($request->wantsJson()){
                return ResponseResult::Failure([$e->getMessage()])  ;
            }
        });

        $exceptions->render(function (AuthenticationException $e, \Illuminate\Http\Request $request) {
            if($request->wantsJson()){
                ResponseResult::Failure([$e->getMessage()]);
            }
        });

        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if($request->wantsJson()){
                return ResponseResult::Failure([$e->getMessage()]);
            }
        });
    })->create();
