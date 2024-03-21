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
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get the request input per page in query params
        $per_page = $request->input('per_page', 15);

        $transaction = QueryBuilder::for(Transaction::class)
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
            ])
            ->paginate($per_page);

        //append it to the transaction variable
        $transaction->appends(['per_page' => $per_page]);

        return new TransactionCollection($transaction);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function store(StoreTransactionRequest $request)
    {
        //check if the user is logged in
        $user = Auth::user();
        $validated_data = $request->validated();

        $total_items = 0;
        $total_amount = 0;

        foreach ($validated_data['products'] as $product_data) {
            //get the product_id 
            $product = Product::find($product_data['product_id']);

            //get the product srp from the db
            $produt_price = $product->srp;

            if (!$product) {
                return response()->json("$product->id not found");
            }

            //get the qty and srp from the request 
            $qty = $product_data['quantity'];
            $srp = $product_data['srp'];

            //compare if the req qty payload is > product qty from the db
            if ($qty > $product->quantity) {
                return response()->json('The selected product is out of stock!');
            }

            //check if the srp is the same in the db
            if ($srp !== $produt_price) {
                return response()->json('The selected product has the wrong SRP');
            }

            //decrement the qty from the db based on qty request
            $product->decrement('quantity', $qty);

            //update the total amount and total qty
            $total_items += $qty;
            $total_amount += $qty * $srp;
        }

        $validated_data['number_of_items'] = $total_items;
        $validated_data['amount_due'] = $total_amount;

        $transaction = $user->transactions()->create($validated_data);

        return response()->json([
            'data' => new TransactionResource($transaction),
            'message' => 'Transaction succesfully added',
        ]);
    }

    public function show(int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(HttpStatusMessage::$NOT_FOUND);
        }
        return new TransactionResource($transaction);
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
        Transaction::find($transaction->id)->delete();
    }
}
