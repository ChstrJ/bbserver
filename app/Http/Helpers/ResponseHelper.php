<?php

namespace App\Http\Helpers;

use App\Models\Product;
use App\Models\Transaction;
use HttpStatusCode;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function transactionResponse(Transaction $transaction, string $message = null): JsonResponse
    {
        $data = [
            "id" => $transaction->id,
            "user_id" => $transaction->user_id,
            "amount_due" => $transaction->amount_due,
            "number_of_items" => $transaction->number_of_items,
            "payment_type" => $transaction->payment_type,
            "created_at" => $transaction->created_at,
            "updated_at" => $transaction->updated_at,
            "message" => $message
        ];
        return response()->json($data, HttpStatusCode::$CREATED);
    }

    public static function productResponse(Product $product, string $message = null): JsonResponse
    {
        $data = [
            "id" => $product->id,
            "category_id" => $product->category_id,
            "name" => $product->name,
            "description" => $product->description,
            "quantity" => $product->quantity,
            "srp" => $product->srp,
            "member_price" => $product->member_price,
            "created_at" => $product->created_at,
            "updated_at" => $product->updated_at,
            "message" => $message
        ];
        return response()->json($data, HttpStatusCode::$CREATED);
    }
}
