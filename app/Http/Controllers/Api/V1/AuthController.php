<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpStatusCode;
use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $credentials = $request->validated();

        if (!Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            return response()->json([
                'message' => "Invalid Credentials",
                'errors' => [
                    "username" => "username or password is incorrect",
                    "password" => "password or password is incorrect",
                ]
            ], HttpStatusCode::$UNAUTHORIZED);
        }

        //get the authenticated user and get the token from the user model
        $user = Auth::user();
        $token = $request->user()->createToken('barista-token')->plainTextToken;

        //return the response with bearer
        return response()->json(['user' => $user])
                        ->withHeaders(['Authorization' => "Bearer {$token}"]);
    }

    public function logout(Request $request)
    {
        $request->auth()->user()->tokens()->delete();
        return response([
            "message" => "Logout Success"
        ], 200);
    }
}
