<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GenericMessage;
use App\Http\Helpers\HttpStatusMessage;
use App\Http\Helpers\ResponseHelper;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Support\Facades\Log;

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
            return response()->json([
                "message" => HttpStatusMessage::$NOT_FOUND
            ], 404);
        }
        $validated_data = $request->validated();
        $transaction->update($validated_data);
        return response()->json([
            "data" => $transaction,
            "message" => GenericMessage::producUpdated($transaction->name)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $transaction = Transaction::destroy($id);
        if (!$transaction) {
            return response()->json(["message" => HttpStatusMessage::$BAD_REQUEST], 400);
        }
        return response()->json(["message" => 'Deleted Success!'], 200);
    }
}
