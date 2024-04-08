<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Utils\HttpStatusCode;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $user = User::with('transactions', 'products')->get();

       return new UserCollection($user);
    }

    public function show(User $user)
    {
        return new UserResource($user->load('transactions', 'products'));
    }

}
