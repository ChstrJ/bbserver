<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\DynamicMessage;
use App\Http\Helpers\GenericMessage;
use App\Http\Helpers\HttpStatusMessage;
use App\Http\Helpers\utils\transaction\TransactionService;
use App\Http\Helpers\utils\transaction\TransactionStatus;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\TransactionResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        //filter date range
        $startDate = $request->input('filter.created_at.0');
        $endDate = $request->input('filter.created_at.1');

        //get the request input per page in query params
        $per_page = $request->input('per_page');

        $query = QueryBuilder::for(Transaction::class)
            ->allowedSorts([
                'amount_due',
                'number_of_items',
                'created_at',
                'status'
            ])
            ->allowedFilters([
                'amount_due',
                'number_of_items',
                'created_at',
                'status'
            ]);

        if($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $query->with('customer');

        $transaction = $query->paginate($per_page);

        return new TransactionCollection($transaction);
    }
    
    public function store(StoreTransactionRequest $request)
    {
        //check if the user is logged in
        $user = Auth::user();
        $validated_data = $request->validated();

        //reduce the qty from db and auto compute items & amount
        $data = TransactionService::ProcessTransaction($validated_data);

        //attach to the payload
        $validated_data['number_of_items'] = $data['total_items'];
        $validated_data['amount_due'] = $data['total_amount'];

        $transaction = $user->transactions()->create($validated_data);
        
        return response()->json([
            'data' => new TransactionResource($transaction),
            'message' => DynamicMessage::transactionAdded($user->username),
        ]);
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load('customer'));
    }

    // public function update(UpdateTransactionRequest $request, int $id)
    // {

    //     $transaction = Transaction::find($id);
    //     if (!$transaction) {
    //         return response()->json(["message" => HttpStatusMessage::$NOT_FOUND], 404);
    //     }
    //     $validated_data = $request->validated();
    //     $transaction->update($validated_data);
    //     return response()->json([
    //         "data" => $transaction, 
    //         "message" => "Transaction updated"], 200);
    // }
    public function destroy(Transaction $transaction)
    {
        Transaction::find($transaction->id)->delete();
    }

    
    public function approve(Transaction $transaction, int $id) {

        $transaction = Transaction::find($transaction->$id);
        if (!$transaction) { 
            return response()->json(["message"=> HttpStatusMessage::$NOT_FOUND], 404);
        }
        $transaction->status = TransactionStatus::$APPROVE;
        $transaction->save();
    }   

    public function reject(Transaction $transaction, int $id) {
        $transaction = Transaction::find($id);
        if (!$transaction) { 
            return response()->json(["message"=> HttpStatusMessage::$NOT_FOUND],404);
        }
        $transaction->status = TransactionStatus::$REJECT;
        $transaction->save();
    }
}
