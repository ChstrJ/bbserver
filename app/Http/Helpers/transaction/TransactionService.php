<?php

namespace App\Http\Helpers\transaction;

use App\Http\Helpers\enums\PaymentMethod;
use App\Http\Helpers\user\UserService;
use App\Models\Product;
use App\Models\Transaction;
use Exception;

trait TransactionService
{
    public static function processTransaction($data)
    {

        $total_items = 0;
        $total_amount = 0;
        $commission = 0;

        foreach ($data['checkouts'] as $product_data) {
            //get the product_id 
            $product = Product::find($product_data['id']);

            $mem_price = $product->member_price;

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


            //update the total amount and total qty
            $total_items += $qty;
            $total_amount += $qty * $srp;

            //compute the commission
            $commission += $srp - $mem_price;
        }

        return ['total_amount' => $total_amount, 'total_items' => $total_items, 'commission' => $commission];
    }

    public static function decrementQty($data)
    {

        foreach ($data as $checkouts) {
            $product = Product::find($checkouts['id']);

            $qty = $checkouts['quantity'];

            if (!$product) {
                throw new Exception('Product ID not found.');
            }

            $product->decrement('quantity', $qty);
        }

    }

    public static function generateReference()
    {
        $start = 0;
        $rand = strtoupper(substr(uniqid(), 9));
        return 'BB' . now()->format('Ymd') . $rand;
    }

    public static function generateFilename()
    {
        $date = UserService::getDate();
        $rand = substr(uniqid(), 10);
        return "sales-{$date}-{$rand}.xlsx";
    }


    // FOR IMAGE UPLOADING IMAGE
    // ENABLE THIS FEATURE IF NEEDED

    // public static function uploadPayment($image)
    // {
    //     if ($image->has('image')) {
    //         $file = $image->file('image');

    //         $extension = $file->getClientOriginalExtension();
    //         $filename = time() . '.' . $extension;

    //         $path = 'uploads/image/';
    //         $file->move($path, $filename);

    //         return $path . $filename;
    //     }
    // }

    public static function toMethod(int $payments)
    {
        switch ($payments) {
            case 1:
                return $payments = PaymentMethod::$CASH;
            case 2:
                return $payments = PaymentMethod::$COD;
            default:
                return 'N/A';
        }
    }

    public static function processCheckouts($data)
    {
        $names = [];

        for ($i = 0; $i < count($data); $i++) {
            $names[] = $data[$i]['name'];
        }

        return implode(', ', $names);
    }

    public static function getChartSalesData($interval)
    {
        $now = UserService::getDate();
        $salesQ = Transaction::select(['status', 'amount_due', 'created_at']);

        switch ($interval) {
            case 'weekly':
                $salesQ->whereRaw('WEEKOFYEAR(created_at) = WEEKOFYEAR(CURDATE())');
                break;
            case 'monthly':
                $salesQ->whereMonth('created_at', '=', date('m'));
                break;
            case 'yearly':
                $salesQ->whereYear('created_at', '=', date('Y'));
                break;
            default:
                $salesQ->where('created_at', $now);
        }
        return number_format($salesQ->where('status', TransactionStatus::$APPROVE)->sum('amount_due'), 2);
    }
}