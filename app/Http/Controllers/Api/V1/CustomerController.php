<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
        return new CustomerResource($customer->load('transactions'));
    }

}
