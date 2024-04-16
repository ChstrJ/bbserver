<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\UserCollection;
use App\Http\Utils\Roles;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AdminController extends Controller
{
    public function getAllTotal()
    {
        $products = Product::count();
        $customer = Customer::count();
        $employee = User::where('role_id', Roles::$EMPLOYEE)->count();

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

    public function filterSales(Request $request)
    {
        //filter date range
        $startDate = $request->input('filter.created_at.0');
        $endDate = $request->input('filter.created_at.1');

        //get the request input per page in query params
        $per_page = $request->input('per_page');

        $query = QueryBuilder::for(Transaction::class)
            ->allowedSorts([
                'reference_number',
                'amount_due',
                'number_of_items',
                'created_at',
                'status',
                'user_id',
                'customer_id'
            ])
            ->allowedFilters([
                'reference_number',
                'amount_due',
                'number_of_items',
                'created_at',
                'status',
                'user_id',
                'customer_id'
            ])
            ->orderByDesc('created_at');

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $query->with('customer', 'user');

        $transaction = $query->paginate($per_page);

        return new TransactionCollection($transaction);
    }

    public function filterEmployees(Request $request)
    {

        $per_page = $request->input('per_page');

        $user = QueryBuilder::for(User::class)
            ->allowedFilters([
                'full_name',
                'is_active',
                'username',
                'last_login_at',
                'last_logout_at',
            ])
            ->allowedSorts([
                'full_name',
                'is_active',
                'username',
                'last_login_at',
                'last_logout_at',
            ])
            ->whereNot('role_id', Roles::$ADMIN)
            ->orderByDesc('last_login_at')
            ->with('products', 'transactions')
            ->paginate($per_page);

        return new UserCollection($user);
    }


}
