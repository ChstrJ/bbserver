<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;

class AdminController extends Controller
{
    public function getAllTotal() {
        $products = Product::all();
        $transations = Transaction::all();
        $customer = Customer::all();

        return response()->json([
            "products" => $products,
            "transations" => $transations,
            "customer" => $customer,
        ]);
    }
}
