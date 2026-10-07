<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateShopRequest;
use App\Models\ErrorMessages;
use App\Models\ResponseResult;
use App\Models\Shop;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class ShopController extends Controller
{
    public function index()
    {
        try {
            return ResponseResult::success(auth()->user());
        }catch (Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }
    public function update(UpdateShopRequest $request, Shop $shop)
    {
        try{
            AuthService::checkUserAccess($shop->id);
            $language = $request->input("language");
            if(! ErrorMessages::isLanguageValid($language)){
                throw new Exception(ErrorMessages::getMessage($shop->language, 'invalid_language', $language));
            }
            $shop->update([
                'name' => $request->input('name'),
                'language' => $language,
            ]);
            return ResponseResult::success($shop);
        }catch (\Throwable $throwable){
            return ResponseResult::Failure([$throwable->getMessage()]);
        }
    }
}
