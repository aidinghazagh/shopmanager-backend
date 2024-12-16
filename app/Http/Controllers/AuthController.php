<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\LoginRequest;
use App\Models\ErrorMessages;
use App\Models\ResponseResult;
use App\Models\Shop;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try{
            $defaultLang = $request->input('default_lang', null);
            $phone = $request->input('phone');
            $shop = Shop::where('phone', $phone)->first();
            if(!$shop){
                throw new CustomValidationException([
                    'phone' => [ErrorMessages::getMessage($defaultLang, 'shop_phone_not_found', $phone)],
                ]);
            }
            if(! Hash::check($request->password, $shop->password)){
                throw new CustomValidationException([
                    'password' => [ErrorMessages::getMessage($defaultLang, 'password_not_match')],
                ]);
            }
            $token = $shop->createToken($phone)->plainTextToken;
            $shop->language = $defaultLang;
            $shop->save();
            return ResponseResult::Success((object)['token' => $token]);
        } catch (CustomValidationException $e) {
            return ResponseResult::ValidationError((object) $e->validationErrors);
        } catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }
    public function logout(Request $request)
    {
        try{
            $shop = auth()->user();
            $shop->tokens()->delete();
            return ResponseResult::Success(null);
        }catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }
}
