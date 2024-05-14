<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\user\UserService;
use App\Http\Helpers\user\UserStatus;
use App\Http\Utils\GenericResponse;
use App\Http\Utils\HttpStatusCode;
use App\Http\Utils\HttpStatusResponse;
use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Utils\Response;
use App\Http\Utils\ResponseHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ResponseHelper;
    public function register(StoreRegisterRequest $request)
    {
        $data = $request->validated();
        $user_data = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'role_id' => 2,
        ]);
        $user = new UserResource($user_data);
        return $this->json($user);
    }

    public function login(StoreLoginRequest $request)
    {

        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return Response::invalidCredentials();
        }

        //get the authenticated user and get the token from the user model
        $user = Auth::user();

        $user->last_login_at = now();
        $user->last_activity = now();
        $user->status = UserStatus::$ONLINE;
        $user->save();

        //create an access token

        $access_token = $request->user()->createToken(name: 'personal-token', expiresAt: now()->addWeek())->plainTextToken;

        //return the response with bearer
        return response()->json(['user' => new UserResource($user), 'token' => $access_token])
            ->withHeaders(['Authorization' => "Bearer {$access_token}"]);
    }

    public function verifyToken(Request $request)
    {
        $user = Auth::user();
        $token = $user->currentAccessToken(); 

        if (!$token || !$user) {
            return response()->json(['token_invalid'], 401);
        }

        if (Carbon::now() >= ($token->expires_at->toDateString())) {
            return response()->json(['token_expired'], 401);
        }
    }

    public function refreshToken(Request $request)
    {

    }
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        $user->last_logout_at = now();
        $user->status = UserStatus::$OFFLINE;
        $user->save();

        return response('', 204);
    }
}
