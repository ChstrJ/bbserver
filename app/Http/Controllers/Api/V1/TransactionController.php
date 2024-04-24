<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\GenericMessage;
use App\Http\Utils\HttpStatusCode;
use App\Http\Utils\HttpStatusMessage;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Models\Product;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\TransactionResource;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionController extends Controller
{
    use ResponseHelper, TransactionService;
    public function index(Request $request)
    {
        //filter date range
        $startDate = $request->input('filter.created_at.0');
        $endDate = $request->input('filter.created_at.1');

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
            ->orderBy('transactions.status')
            ->orderByDesc('transactions.status')
            ->where('user_id', UserService::getUserId());

        //filtering by customer fullname
        if ($request->has('filter.customer.full_name')) {
            $customerName = $request->input('filter.customer.full_name');
            $query->where('customer.full_name', 'LIKE', "%$customerName%");
        }

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            $query->whereBetween('created_at', [$startDate, $endDate]);
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
        $image = TransactionService::uploadPayment($request);

        //attach to payload
        $validated_data['reference_number'] = $reference_number;
        $validated_data['number_of_items'] = $total['total_items'];
        $validated_data['amount_due'] = $total['total_amount'];
        $validated_data['commission'] = $total['commission'];
        $validated_data['image'] = $image;

        $user->transactions()->create($validated_data);

        return $this->json(['message' => DynamicMessage::transactionAdded($user->username)]);
    }

    public function show(int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        return new TransactionResource($transaction->load('customer'));
    }
    public function destroy(Transaction $transaction)
    {
        Transaction::find($transaction->id)->delete();
    }


    public function approve(Transaction $transaction, int $id)
    {

        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }

        $data = $transaction->checkouts;

        TransactionService::decrementQty($data);

        if ($transaction->status === TransactionStatus::$APPROVE) {
            return $this->json(Message::alreadyApproved(), HttpStatusCode::$CONFLICT);
        }
        $transaction->status = TransactionStatus::$APPROVE;
        $transaction->save();
        return $this->json(Message::approve());

    }

    public function reject(Transaction $transaction, int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        if ($transaction->status === TransactionStatus::$REJECT) {
            return $this->json(Message::alreadyRejected(), HttpStatusCode::$CONFLICT);
        }
        $transaction->status = TransactionStatus::$REJECT;
        $transaction->save();
        return $this->json(Message::reject());
    }


}
