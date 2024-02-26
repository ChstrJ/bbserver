<?php
namespace App\Helpers;

class ResponseFormatter
{
    public static function RegisterFormat($user, $token)
    {
        return [
            'user' => [
                'userID' => $user->userID,
                'fullName' => $user->fullName,
                'username' => $user->username,
                'password' => $user->password,
            ],
            'token' => $token,
            'message' => "Register Success"
        ];
    }
}
