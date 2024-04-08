<?php

namespace App\Http\Helpers\transaction;
use App\Models\Product;
use Exception;

class TransactionService {
    public static function ProcessTransaction ($data) {

        $total_items = 0;
        $total_amount = 0;

        foreach ($data['checkouts'] as $product_data) {
            //get the product_id 
            $product = Product::find($product_data['id']);

            //get the product srp from the db
            $produt_price = $product->srp;

            if (!$product) {
                throw new Exception("$product->id not found");
            }

            //get the qty and srp from the request 
            $qty = $product_data['quantity'];
            $srp = $product_data['srp'];
            $name = $product_data['name'];  

            //compare if the req qty payload is > product qty from the db
            if ($qty > $product->quantity) {
                throw new Exception('The selected product is out of stock!');
            }

            if ($product->name !== $name) {
                throw new Exception('Invalid product name');
            }
            
            //check if the srp is the same in the db
            if ($srp !== $produt_price) {
                throw new Exception('Invalid product SRP');
            }

            //decrement the qty from the db based on qty request
            $product->decrement('quantity', $qty);

            //update the total amount and total qty
            $total_items += $qty;
            $total_amount += $qty * $srp;
        }

        return ['total_amount' => $total_amount, 'total_items' => $total_items];
    }
}