<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register (StoreUserRequest $request) {
        $data = $request->validated();
        $user_data = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            
        ]);
        $token = $user_data->createToken('barista-token')->plainTextToken;
        return ResponseHelper::registerResponse($user_data, $token, "Login Success");
            
    }

    public function login (Request $request) {
        $user_data = User::where('username', $request['username'])->first();
        if(!$user_data || !Hash::check($request['password'], $user_data->password)) {
            return response()->json(["message" => "Invalid Username and Password!"] ,401);
        } else {
            $token = $user_data->createToken('barista-token')->plainTextToken;
            return response()->json([
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
