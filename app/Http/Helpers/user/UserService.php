<?php

namespace App\Http\Helpers\user;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService {

    public static function getUser() {
        return Auth::user();
    }
    public static function getUserId() {
        return Auth::user()->id;
    }
    public static function getUsernameById(int $id) {
        return User::find($id)->username;
    }
   
}