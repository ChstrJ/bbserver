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
use Tymon\JWTAuth\Facades\JWTAuth;

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
        // $user_data = User::where('username', $request['username'])->first();
        // if (!$user_data || !Hash::check($request['password'], $user_data->password)) {
        //     return response()->json([
        //         'message' => "Invalid Credentials",
        //         'errors' => [
        //             "username" => "username or password is incorrect",
        //             "password" => "password or password is incorrect",
        //         ]
        //     ], HttpStatusCode::$UNAUTHORIZED);
        // }
        // $access_token = $user_data->createToken('barista-token')->plainTextToken;
        // return response()->json($user_data)->withHeaders([
        //     'Authorization' => "Bearer {$access_token}"
        // ]);

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

        //token from jwt
        $token = JWTAuth::fromUser($user);

        //token from sanctum
        // $token = $request->user()->createToken('barista-token')->plainTextToken;

        //store jwt in cookie
        $cookies = cookie('jwt', $token);

        //return the response with cookies and bearer
        return response()->json(['user' => $user])
                        ->withCookie($cookies)
                        ->withHeaders(['Authorization' => "JWT Bearer {$token}"]);
    }

    public function logout(Request $request)
    {
        $request->auth()->user()->tokens()->delete();
        return response([
            "message" => "Logout Success"
        ], 200);
    }
}
