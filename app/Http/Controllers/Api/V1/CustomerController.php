<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\GenericMessage;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerResource;
use Spatie\QueryBuilder\QueryBuilder;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('transactions')->get();
        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        $validated_data = $request->validated();
        $customer = Customer::create($validated_data);
        return new CustomerResource($customer); 
    }

    public function show(Customer $customer)
    {
        if(!$customer) {
            return response()->json(GenericMessage::$UNDEFINED_USER);
        }
        return new CustomerResource($customer->load('transactions'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer) {
        $data = $request->validated(); 
        $customer->update($data);
        if (!$customer->update($data)) {
            return response()->json(GenericMessage::$INVALID, 422);
        }
        return response()->json(DynamicMessage::customerUpdated($data['name']));
    }

    public function destroy (Customer $customer) {
        $user = Customer::find($customer->id);
        if(!$user) {
            return response()->json(GenericMessage::$UNDEFINED_USER);
        }
        $user->delete();
        return response()->json(DynamicMessage::customerRemove($customer->name));
    }

}
