<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Utils\GenericMessage;
use App\Http\Utils\HttpStatusCode;
use App\Http\Utils\HttpStatusMessage;
use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        return response()->json([
            'data' => new UserResource($user_data),
            'message' => 'Register successfully.'
        ]);
    }

    public function login(StoreLoginRequest $request)
    {

        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => GenericMessage::$INVALID_CREDENTIALS,
            ], 401);
        }

        //get the authenticated user and get the token from the user model
        $user = Auth::user(); 

        $user->last_login_at = now('Asia/Manila');
        $user->save();

        //create an access token
        $access_token = $request->user()->createToken('barista-token')->plainTextToken;

        //return the response with bearer
        return response()->json(['user' => $user, 'token' => $access_token])
            ->withHeaders(['Authorization' => "Bearer {$access_token}"]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        $user->last_logout_at = now('Asia/Manila');
        $user->save();

        return response()->json(["message" => "Logout Success"], HttpStatusCode::$ACCEPTED);
    }
}
