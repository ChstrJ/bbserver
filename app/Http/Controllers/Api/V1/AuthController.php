<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\user\UserService;
use App\Http\Helpers\user\UserStatus;
use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        return Response::createResource();
    }

    public function login(StoreLoginRequest $request)
    {

        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->json(Message::invalidCredentials(), HttpStatusCode::$UNAUTHORIZED);
        }

        //get the authenticated user and get the token from the user model
        $user = Auth::user();
        $user->last_login_at = now();
        $user->last_activity = now();
        $user->status = UserStatus::$ONLINE;
        $user->save();

        //create an access token

        $user = new UserResource($user);

        $access_token = $request->user()->createToken(name: 'personal-token', expiresAt: now()->addWeek())->plainTextToken;

        //return the response with bearer token with cookie
        return response()->json(['user' => $user, 'token' => $access_token])
            ->withHeaders(['Authorization' => "Bearer {$access_token}"]);

    }

    public function verifyToken(Request $request)
    {
        $user = Auth::user();
        $token = $user->currentAccessToken();
        
        if (!$token || !$user) {
            return response()->json(['token_invalid'], 401);
        }

        if (Carbon::now()->toDateString() >= ($token->expires_at->toDateString())) {
            return response()->json(['token_expired'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        $user->last_logout_at = now();
        $user->status = UserStatus::$OFFLINE;
        $user->save();

        $user->currentAccessToken()->delete();

        return response('', 204);
    }

    public function getUserInfo(Request $request)
    {
        $id = UserService::getUserId();
        $info = User::find($id);
        return new UserResource($info);
    }

}
