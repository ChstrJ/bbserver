<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\customer\CustomerStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Resources\V1\CustomerCollection;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\Response;
use App\Http\Utils\ResponseHelper;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ResponseHelper;
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        
        $query = Customer::query()
        ->whereNot('is_active', CustomerStatus::$NOT_ACTIVE)
        ->with('transactions', 'user')
        ->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('email_address', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->paginate($perPage);
        return new CustomerCollection($customers);
    }

    public function store(StoreCustomerRequest $request, Customer $customer)
    {
        $user = UserService::getUser();
        $validated_data = $request->validated();
        $validated_data['created_by'] = $user->id;
        $user = Customer::create($validated_data);
        if (!$user) {
            return Response::invalid();
        }
        return $this->json(DynamicMessage::customerAdded($validated_data['full_name']));
    }

    public function show(int $id)
    { 
        $customer = Customer::find($id);    
        if(!$customer) {
            return Response::notFound();
        }
        return new CustomerResource($customer->load('transactions', 'user'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $user = UserService::getUser();
        $validated_data = $request->validated();
        $validated_data['updated_by'] = $user->id;
        $customer->update($validated_data);
        if (!$customer) {
            return Response::invalid();
        }
        return $this->json(DynamicMessage::customerUpdated($customer->full_name));
    }

    public function destroy(int $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return Response::notFound();
        }
        if($customer->is_active === CustomerStatus::$NOT_ACTIVE) {
            return Response::alreadyChanged();
        }

        $customer->is_active = CustomerStatus::$NOT_ACTIVE;
        $customer->save();
        return $this->json(DynamicMessage::customerRemove($customer->full_name));
    }

}