<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register (Request $request) {
        $data = $request->validate([
            'full_name' => 'required|string|min:2',
            'username' => 'required|string|min:2|unique:users,username',
            'password' => 'required|string|min:2'
        ]);
        $user_data = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            
        ]);
        $message = "{$user_data->username} successfully registered";
        $token = $user_data->createToken('barista-token')->plainTextToken;
        $response = [
            'message' => $message,
            'user' => $user_data,
            'token' => $token
        ];
        return response($response, 201);
            
    }

    public function login (Request $request) {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        $user_data = User::where('username', $data['username'])->first();
        $token = $user_data->createToken('barista-token')->plainTextToken;
        if(!$user_data || !Hash::check($data['password'], $user_data->password)) {
            return response([
                "message" => "Bad Credentials",
            ], 401);
        } else {
            return response([
                "message" => "Login Success",
                "user" => $user_data,
                "token" => $token
            ], 200);
        }
    }

    public function logout (Request $request) {
        auth()->user()->tokens()->delete();
        return response([
            "message" => "Logout Success"
        ], 200);
    }
}
