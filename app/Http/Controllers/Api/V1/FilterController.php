<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\UserCollection;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function filterSales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 15);
        $customerName = $request->input('customer');
        $searchByRefNo = $request->input('search_by_ref');
        $sortByDesc = $request->input('sort_by_desc');
        $sortByAsc = $request->input('sort_by_asc');
        $status = $request->input('status');
        $paymentMethod = $request->input('payment_method');
        $employeeId = $request->input('employee_id');

        $query = Transaction::query()
            ->select('transactions.*')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->whereNot('transactions.is_removed', TransactionStatus::$REMOVE)
            ->where('transactions.status', TransactionStatus::$APPROVE)
            ->with('user', 'customer');

        if ($startDate && $endDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate);
        }

        if ($status) {
            $query->where("transactions.status", $status);
        }

        if ($paymentMethod) {
            $query->where("transactions.payment_method", $paymentMethod);
        }

        if ($sortByDesc) {
            $query->orderBy("transactions.$sortByDesc", 'DESC');
        }

        if ($sortByAsc) {
            $query->orderBy("transactions.$sortByAsc", 'ASC');
        }

        if ($searchByRefNo) {
            $query->where('transactions.reference_number', 'LIKE', "%{$searchByRefNo}%");
        }

        //filtering by customer fullname
        if ($customerName) {
            $query->where('customers.full_name', 'LIKE', "%{$customerName}%");
        }

        $commission = 0;
        $sales = 0;

        //filter by employee id and get the commission and sales
        if ($employeeId) {
            $query->where('users.id', $employeeId);

            $commission = $query
                ->where("transactions.status", TransactionStatus::$APPROVE)
                ->where('transactions.user_id', $employeeId)
                ->sum('commission');
            $sales = $query
                ->where("transactions.status", TransactionStatus::$APPROVE)
                ->where('transactions.user_id', $employeeId)
                ->sum('amount_due');
        }

        $transactions = $query->paginate($perPage);

        $transactionCollection = new TransactionCollection($transactions);

        $additionalData = [
            'commission' => number_format($commission, 2) ?: 0,
            'sales' => number_format($sales, 2) ?: 0,
        ];

        return $transactionCollection->additional($additionalData);
    }

    public function filterEmployees(Request $request)
    {

        $perPage = $request->input('per_page', 15);
        $employeeName = $request->input('employee');

        $query = User::query()
            ->select('*')
            ->orderByDesc('last_login_at')
            ->with('products', 'transactions');

        if ($employeeName) {
            $query->where('full_name', 'LIKE', "%$employeeName%");
        }

        $user = $query->paginate($perPage);
        return new UserCollection($user);
    }

    public function filterOrders(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $status = $request->input('status');
        $sortByAsc = $request->input('sort_date_asc');
        $sortByDesc = $request->input('sort_date_desc');
        $paymentMethod = $request->input('payment_method');

        $query = Transaction::query()
            ->select('transactions.*', 'customers.full_name', 'users.full_name')
            ->with('customer', 'user')
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->orderBy('transactions.status', 'ASC')
            ->orderByDesc('transactions.created_at');

        if ($startDate && $endDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate);
        }


        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transactions.reference_number', 'LIKE', "%{$search}%")
                    ->orWhere('users.full_name', 'LIKE', "%{$search}%")
                    ->orWhere('customers.full_name', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where("transactions.status", $status);
        }

        if ($paymentMethod) {
            $query->where("transactions.payment_method", $paymentMethod);
        }

        if ($sortByDesc) {
            $query->orderBy("transactions.$sortByDesc", 'DESC');
        }

        if ($sortByAsc) {
            $query->orderBy("transactions.$sortByAsc", 'ASC');
        }

        $transaction = $query->paginate($perPage);
        return new TransactionCollection($transaction);
    }
}
