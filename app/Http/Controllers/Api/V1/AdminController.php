<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getAllTotal() {
        $products = Product::count();
        $customer = Customer::count();
        $employee = User::where('role_id', 2)->count();

        $sales = Transaction::where('status', 'approved')->sum('amount_due');
        $pending = Transaction::where('status', 'pending')->sum('amount_due');
        $reject = Transaction::where('status', 'rejected')->sum('amount_due');

        $salesCount = Transaction::where('status', 'approved')->count();
        $pendingCount = Transaction::where('status', 'pending')->count();
        $rejectCount = Transaction::where('status', 'rejected')->count();

        return response()->json([
            "totalProducts" => $products,
            "totalCustomers" => $customer,
            "totalEmployee" => $employee,
            "transactionCounts" => [
                "salesCount" => $salesCount,
                "pendingCount" => $pendingCount,
                "rejectCount" => $rejectCount,
            ],
            "transactionsTotal" => [
                'totalSales' => number_format($sales, 2),
                'totalPending' => number_format($pending, 2),
                'totalRejected' => number_format($reject, 2),
            ],
        ]);
    }

    public function filterSales(Request $request) {

    }
}
