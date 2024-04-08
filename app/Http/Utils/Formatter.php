<?php
namespace App\Http\Utils;

class ResponseFormatter
{
    public static function RegisterFormat($user, $token)
    {
        return [
            'user' => [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'password' => $user->password,
            ],
            'token' => $token,
            'message' => "Register Success"
        ];
    }
}
