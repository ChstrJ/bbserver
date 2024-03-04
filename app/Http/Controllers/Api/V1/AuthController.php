<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(StoreRegisterRequest $request)
    {
        $data = $request->validated();
        $user_data = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),

        ]);
        $token = $user_data->createToken('barista-token')->plainTextToken;
        return new UserResource($user_data, $token, "Register Success");
    }

    public function login(StoreLoginRequest $request)
    {
        $user_data = User::where('username', $request['username'])->first();
        if (!$user_data || !Hash::check($request['password'], $user_data->password)) {
            return response()->json(['message' => [
                "username" => "username is invalid",
                "password" => "password is invalid",
            ]], 401);
        }
        $accessToken = $user_data->createToken('barista-token')->plainTextToken;
        return response()->json($user_data)->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->auth()->user()->tokens()->delete();
        return response([
            "message" => "Logout Success"
        ], 200);
    }
}
