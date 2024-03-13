<?php

namespace App\Http\Utils;

use App\Http\Helpers\HttpStatusMessage;
use App\Models\Product;

class TransactionUtils
{
    public static function processProducts($validated_data, $total_amount, $total_items)
    {

        $total_amount = 0;
        $total_items = 0;

        foreach ($validated_data['products'] as $product_data) {
            //get the product_id 
            $product = Product::find($product_data['product_id']);

            //check if the product exs
            if (!$product) {
                return response()->json("$product->id not found");
            }

            //get the qty and srp from the request 
            $qty = $product_data['quantity'];
            $srp = $product_data['srp'];

            //compare if the req qty payload is > product qty from the db
            if ($qty > $product->quantity) {
                return response()->json([HttpStatusMessage::$BAD_REQUEST], 400);
            }

            $product->decrement('quantity', $qty);

            //update the total amount and total items
            $total_items += $qty;
            $total_amount += $qty * $srp;
        }
        return ['total_amount' => $total_amount, 'total_items' => $total_items];
    }
}
