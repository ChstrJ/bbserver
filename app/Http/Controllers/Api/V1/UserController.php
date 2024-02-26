<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpStatusCode;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::all(), HttpStatusCode::$OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {

        // $validated_data = $request->validate([
        //     'full_name' => 'required|string|min:2',
        //     'username' => 'required|string|min:2|unique:users,username',
        //     'password' => 'required|string|min:2',
        //     'is_active' => 'required|boolean',
        // ]);

        // $user = User::create($validated_data);
        // $message = "{$user->username} successfully created";

        // return response()->json([
        //     'message' => $message,
        //     'user' => $user,
        // ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = User::find($id);        
        return response($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $User)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $User)
    {
        //
    }
}
