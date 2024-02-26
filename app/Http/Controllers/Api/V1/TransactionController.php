<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GenericMessage;
use App\Http\Helpers\ResponseHelper;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Transaction::all();
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
        //check if user is logged in
        $user = auth()->user();
        $validated_data = $request->validated();
        $validated_data['user_id'] = $user->id;
        $transaction = Transaction::create($validated_data);
        $message = GenericMessage::transactionAdded($user->username);
        return ResponseHelper::transactionResponse($transaction,  $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return Transaction::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
