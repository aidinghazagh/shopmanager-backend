<?php

namespace App\Services;

use App\Models\ErrorMessages;

class AuthService
{
    public static function checkUserAccess($id)
    {
        if (auth()->id() !== (int) $id){
            throw new \Exception(ErrorMessages::getMessage(auth()->user()->language, 'unauthenticated'));
        } else{
            return true;
        }
    }
}
