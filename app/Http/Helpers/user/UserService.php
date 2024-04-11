<?php

namespace App\Http\Helpers\user;
use Illuminate\Support\Facades\Auth;

class UserService {
    public static function getUsername() {
        return Auth::user()->username;
    }
}