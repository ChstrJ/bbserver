<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\GenericMessage;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->input('per_page');

        $customers = QueryBuilder::for(Customer::class)
            ->allowedFilters([
                'full_name',
                'address',
                'phone_number',
                'address'
            ])
            ->allowedSorts([
                'full_name',
                'address',
                'phone_number',
                'address'
            ])
            ->with('transactions')
            ->orderByDesc('created_at');

        $customers = $customers->paginate($per_page);
        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        $validated_data = $request->validated();
        $customer = Customer::create($validated_data);
        return new CustomerResource($customer);
    }

    public function show(int $id)
    { 
        $customer = Customer::find($id);    
        if(!$customer) {
            return response()->json(GenericMessage::$UNDEFINED_USER, 404);
        }
        return new CustomerResource($customer->load('transactions'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $customer = Customer::update($data);
        if (!$customer) {
            return response()->json(GenericMessage::$INVALID, 422);
        }
        return response()->json(DynamicMessage::customerUpdated($data['name']));
    }

    public function destroy(Customer $customer)
    {
        $user = Customer::find($customer->id);
        if (!$user) {
            return response()->json(GenericMessage::$UNDEFINED_USER);
        }
        $user->delete();
        return response()->json(DynamicMessage::customerRemove($customer->name));
    }

}
