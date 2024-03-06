<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GenericMessage;
use App\Http\Helpers\HttpStatusMessage;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\TransactionResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaction = QueryBuilder::for(Transaction::class)
            ->allowedSorts(['amount_due',  'number_of_items'])
            ->allowedFilters(['amount_due',  'number_of_items'])
            ->paginate();
        return new TransactionCollection($transaction);
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
    public function store(StoreTransactionRequest $request)
    {
        $user = Auth::user();
        $validated_data = $request->validated();
        $transaction = $user->transactions()->create($validated_data);
        return new TransactionResource($transaction);
    }

    public function show(int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(["message" => HttpStatusMessage::$NOT_FOUND], 404);
        }
        return $transaction;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UpdateTransactionRequest $request, int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, int $id)
    {

        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(["message" => HttpStatusMessage::$NOT_FOUND], 404);
        }
        $validated_data = $request->validated();
        $transaction->update($validated_data);
        return response()->json(["data" => $transaction, "message" => "Transaction updated"], 200);
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(["message" => 'Deleted Success!'], 200);
    }
}
