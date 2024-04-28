<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\HttpStatusCode;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionController extends Controller
{
    use ResponseHelper, TransactionService;
    public function index(Request $request)
    {

        //get the request input per page in query params
        $per_page = $request->input('per_page');

        $query = QueryBuilder::for(Transaction::class)
            ->allowedSorts([
                'reference_id',
                'amount_due',
                'number_of_items',
                'created_at',
                'status',
            ])
            ->allowedFilters([
                'reference_id',
                'amount_due',
                'number_of_items',
                'created_at',
                'customer.full_name',
                'status',
            ])
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->where('user_id', UserService::getUserId())
            ->orderByDesc('transactions.created_at');

        //filtering by customer fullname
        if ($request->has('filter.customer.full_name')) {
            $customerName = $request->input('filter.customer.full_name');
            $query->where('customer.full_name', 'LIKE', "%$customerName%");
        }

        $query->with('customer', 'user');

        $transaction = $query->paginate($per_page);

        return new TransactionCollection($transaction);
    }

    public function store(StoreTransactionRequest $request)
    {
        //check if the user is logged in
        $user = Auth::user();
        $validated_data = $request->validated();

        //reduce the qty from db and auto compute items & amount
        $total = TransactionService::processTransaction($validated_data);
        $reference_number = TransactionService::generateReference();

        //attach to payload
        $validated_data['reference_number'] = $reference_number;
        $validated_data['number_of_items'] = $total['total_items'];
        $validated_data['amount_due'] = $total['total_amount'];
        $validated_data['commission'] = $total['commission'];

        $user->transactions()->create($validated_data);

        return $this->json(Message::orderSuccess(), HttpStatusCode::$ACCEPTED);
    }

    public function show(int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        return new TransactionResource($transaction->load('customer'));
    }
    public function destroy(int $id)
    {
        $order = Transaction::find($id);
        if(!$order) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND); 
        }
        if($order->is_removed === TransactionStatus::$REMOVED) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }
        $order->is_removed = TransactionStatus::$REMOVED;
        $order->save();

        return $this->json(Message::orderRemoved(), HttpStatusCode::$ACCEPTED);
    }

}
