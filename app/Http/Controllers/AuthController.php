<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register (Request $request) {
        $data = $request->validate([
            'fullName' => 'required|string|min:2',
            'username' => 'required|string|min:2|unique:users,username',
            'password' => 'required|string|min:2'
        ]);
        $user_data = User::create([
            'fullName' => $data['fullName'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            
        ]);
        $token = $user_data->createToken('barista-token')->plainTextToken;
        $response = [
            'user' => [
                'userID' => $user_data->userID,
                'fullName' => $user_data->fullName,
                'username' => $user_data->username,
                'password' => $user_data->password,
            ],
            'token' => $token,
            'message' => "Register Success"
        ];
        return response($response, 201);
            
    }

    public function login (Request $request) {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        $user_data = User::where('username', $data['username'])->first();
        if(!$user_data || !Hash::check($data['password'], $user_data->password)) {
            return response([
                "message" => "Bad Credentials",
            ], 401);
        } else {
            $token = $user_data->createToken('barista-token')->plainTextToken;
            return response([
                "user" => $user_data,
                "token" => $token,
                "message" => "Login Success"
            ], 200);
        }
    }

    public function logout (Request $request) {
        $request->auth()->user()->tokens()->delete();
        return response([
            "message" => "Logout Success"
        ], 200);
    }
}
