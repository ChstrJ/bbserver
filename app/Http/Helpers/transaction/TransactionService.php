<?php

namespace App\Http\Helpers\transaction;

use App\Http\Helpers\enums\PaymentMethod;
use App\Http\Helpers\user\UserService;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;

class TransactionService
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

        return [
            'total_amount' => $total_amount,
            'total_items' => $total_items,
            'commission' => $commission
        ];
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

        $names = array_map(function ($item) {
            return "{$item['name']} {$item['quantity']} qty";
        }, $data);

        return implode(', ', $names);
    }


    public static function getLogScaleData(string $interval, int $userId = null)
    {
        $now = Carbon::now();

        $query = Transaction::query();

        $startWeek = $now->startOfWeek()->toDateTimeString();
        $endWeek = $now->endOfWeek()->toDateTimeString();

        $startYear = $now->startOfYear()->toDateString();
        $endYear = $now->endOfYear()->toDateString();

        switch ($interval) {
            case 'weekly':

                $weeklySales = [
                    'Monday' => 0,
                    'Tuesday' => 0,
                    'Wednesday' => 0,
                    'Thursday' => 0,
                    'Friday' => 0,
                    'Saturday' => 0,
                    'Sunday' => 0,
                ];

                $weeklySalesData = $query
                    ->selectRaw('DATE(created_at) AS day, TRUNCATE(SUM(amount_due), 2) AS total_sales')
                    ->when($userId, fn($q, $userId) => $q->where('user_id', $userId))
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereBetween('created_at', [$startWeek, $endWeek])
                    ->groupByRaw('DATE(created_at)')
                    ->get();

                for ($day = 0; $day < count($weeklySalesData); $day++) {
                    $dayName = Carbon::parse($weeklySalesData[$day]->day)->format('l');
                    $weeklySales[$dayName] = $weeklySalesData[$day]->total_sales;
                }

                return $weeklySales;

            case 'monthly':

                $monthSales = [];

                $monthlySales = $query
                    ->selectRaw('MONTH(created_at) AS month, TRUNCATE(SUM(amount_due), 2) AS total_sales')
                    ->when($userId, fn($q, $userId) => $q->where('user_id', $userId))
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereBetween('created_at', [$startYear, $endYear])
                    ->groupByRaw('MONTH(created_at)')
                    ->get();

                for ($month = 1; $month < 12 + 1; $month++) {
                    $monthName = Carbon::createFromDate(null, $month)->format('F');

                    $monthName = substr($monthName, 0, 3);
                    $salesData = $monthlySales->where('month', $month)->first();
                    $monthSales[$monthName] = $salesData ? $salesData->total_sales : 0;
                }

                return $monthSales;

            case 'yearly':

                $yearSales = [];

                //start with the last 5 years through the current yhear
                $startYear = $now->year - 4;
                $endYear = $now->year;

                $yearlySalesData = $query
                    ->selectRaw('YEAR(created_at) AS year, SUM(amount_due) AS total_sales')
                    ->when($userId, fn($q, $userId) => $q->where('user_id', $userId))
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 5 YEAR)')
                    ->groupByRaw('YEAR(created_at)')
                    ->get()
                    ->keyBy('year');

                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearSales[$year] = $yearlySalesData->has($year) ? $yearlySalesData[$year]->total_sales : 0;
                }

                return $yearSales;

            default:
                $todaySales = $query
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereRaw('created_at = ?', [UserService::getDate()])
                    ->sum('amount_due');

                return ["today_sales" => $todaySales];
        }
    }
}