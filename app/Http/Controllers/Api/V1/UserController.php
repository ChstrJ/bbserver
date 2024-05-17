<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\user\UserStatus;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Utils\Response;
use App\Http\Utils\ResponseHelper;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ResponseHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = User::with('transactions', 'products')
            ->where('is_active', UserStatus::$ACTIVE);

        if ($search) {
            $query->where('full_name', 'LIKE', "%{$search}%")
                ->orWhere('username', 'LIKE', "%{$search}%");
        }

        $user = $query->paginate($perPage);
        return new UserCollection($user);
    }

    public function store(StoreRegisterRequest $request)
    {
        $data = $request->validated();
        User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'role_id' => 2,
        ]);
        return Response::createResource();
    }

    public function show(User $user)
    {
        return new UserResource($user->load('transactions', 'products'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated_data = $request->validated();
        $user->update($validated_data);
        if (!$user) {
            return Response::invalid();
        }
        return Response::updateResource();
    }

    public function destroy(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return Response::notFound();
        }
        if ($user->is_active === UserStatus::$NOT_ACTIVE) {
            return Response::alreadyChanged();
        }

        $user->is_active = UserStatus::$NOT_ACTIVE;
        $user->save();
        return Response::deleteResource();
    }

}
