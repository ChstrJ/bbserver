<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\UserCollection;
use App\Http\Utils\Roles;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class AdminController extends Controller
{
    public function getAllTotal()
    {
        $today = UserService::getDate();

        $products = Product::count();
        $customer = Customer::count();
        $employee = User::where('role_id', Roles::$EMPLOYEE)->count();
        
        $sales = Transaction::where('status', TransactionStatus::$APPROVE)->sum('amount_due');
        $reject = Transaction::where('status', TransactionStatus::$REJECT)->sum('amount_due');
        $pending = Transaction::where('status', TransactionStatus::$PENDING)->sum('amount_due');
        
        $sales_count = Transaction::where('status', TransactionStatus::$APPROVE)->count();
        $reject_count = Transaction::where('status', TransactionStatus::$REJECT)->count();
        $pending_count = Transaction::where('status', TransactionStatus::$PENDING)->count();

        $today_sales = Transaction::where('status', TransactionStatus::$APPROVE)
                                    ->whereDate('created_at', $today)
                                    ->sum('amount_due');

        return response()->json([
            "total_products" => $products,
            "total_customers" => $customer,
            "total_employees" => $employee,
            "transaction_counts" => [
                "sales_count" => $sales_count,
                "pending_count" => $pending_count,
                "reject_count" => $reject_count,
            ],
            "transactions_total" => [
                "today_sales" => floatval($today_sales),
                "total_sales" => floatval($sales),
                "total_pending" => floatval($pending),
                "total_rejected" => floatval($reject),
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
                'customer_id',
                'commission'
            ])
            ->allowedFilters([
                'reference_number',
                'amount_due',
                'number_of_items',
                'created_at',
                'status',
                'user_id',
                'customer_id',
                'commission'
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
            // ->whereNot('role_id', Roles::$ADMIN)
            ->orderByDesc('last_login_at')
            ->with('products', 'transactions')
            ->paginate($per_page);

        return new UserCollection($user);
    }


}
