<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\user\UserService;
use App\Http\Resources\V1\CustomerCollection;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\GenericMessage;
use App\Http\Utils\HttpStatusCode;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class CustomerController extends Controller
{
    use ResponseHelper;
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
            ->with('transactions', 'user')
            ->orderByDesc('created_at');

        $customers = $customers->paginate($per_page);
        return new CustomerCollection($customers);
    }

    // public function store(StoreCustomerRequest $request)
    // {
    //     $user = UserService::getUserId();
    //     $validated_data = $request->validated();
    //     $validated_data['created_by'] = $user;
    //     $customer = Customer::create($validated_data);
    //     return new CustomerResource($customer);
    // }

    public function store(StoreCustomerRequest $request, Customer $customer)
    {
        $user = UserService::getUser();
        $validated_data = $request->validated();
        $validated_data['created_by'] = $user->id;
        $user = Customer::create($validated_data);
        if (!$user) {
            return $this->json(Message::invalid(), HttpStatusCode::$UNPROCESSABLE_ENTITY);
        }
        return $this->json(DynamicMessage::customerAdded($validated_data['full_name']));
    }

    public function show(int $id)
    { 
        $customer = Customer::find($id);    
        if(!$customer) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
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
            return $this->json(Message::invalid(), HttpStatusCode::$UNPROCESSABLE_ENTITY);
        }
        return $this->json(DynamicMessage::customerUpdated($customer->full_name));
    }

    public function destroy(int $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        $customer->delete();
        return $this->json(DynamicMessage::customerRemove($customer->full_name));
    }

}
