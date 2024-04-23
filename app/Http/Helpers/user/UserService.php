<?php

namespace App\Http\Helpers\user;
use App\Models\User;
use Carbon\Carbon;
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

    public static function getFullnameById(int $id) {
        return User::find($id)->full_name;
    }
    

    public static function getUsername() {
        $id = Auth::user()->id;
        return User::find($id)->username;
    }

    public static function getDate(){
        return Carbon::today()->format("Y-m-d");
    }
   
}