<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\UserCollection;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function filterSales(Request $request)
    {

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page');
        $customerName = $request->input('customer');
        $employeeName = $request->input('employee');
        $searchByRefNo = $request->input('search_by_ref');
        $sortByDateAsc = $request->input('sort_date_asc');
        $sortByDateDesc = $request->input('sort_date_desc');
        $commission = $request->input('commission');

        $query = Transaction::query()
            ->select('transactions.*')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->orderBy('transactions.status')
            ->with('user', 'customer');

        if ($startDate && $endDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate);
        }

        if ($sortByDateDesc === null) {
            $query->orderBy('transactions.created_at', 'DESC');
        }

        if ($sortByDateAsc === null) {
            $query->orderBy('transactions.created_at', 'ASC');
        }
        
        if ($searchByRefNo) {
            $query->where('transactions.reference_number', 'LIKE', "%$searchByRefNo%");
        }

        //filtering by customer fullname
        if ($customerName) {
            $query->where('customers.full_name', 'LIKE', "%$customerName%");
        }

        //filtering by user fullname
        if ($employeeName) {
            $query->where('users.full_name', 'LIKE', "%$employeeName%");
        }

        $perPage = $perPage ?: 15;

        $transactions = $query->paginate($perPage);

        return new TransactionCollection($transactions);
    }

    public function filterEmployees(Request $request)
    {

        $perPage = $request->input('per_page');
        $employeeName = $request->input('employee');

        $query = User::query()
            ->select('*')
            ->orderByDesc('last_login_at')
            ->with('products', 'transactions');


        if ($employeeName) {
            $query->where('full_name', 'LIKE', "%$employeeName%");
        }

        $perPage ?: 15;

        $user = $query->paginate($perPage);

        return new UserCollection($user);
    }

    public function filterOrders(Request $request)
    {

        $perPage = $request->input('per_page');
        $employeeName = $request->input('employee');
        $customerName = $request->input('customer');
        $sortByDateAsc = $request->input('sort_date_asc');
        $sortByDateDesc = $request->input('sort_date_desc');

        $query = Transaction::query()
            ->select('transactions.*', 'customers.full_name', 'users.full_name')
            ->with('customer', 'user')
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->where('transactions.status', 'pending')
            ->orderByDesc('transactions.created_at');

        if($employeeName) {
            $query->where('user.full_name', 'LIKE', "%$employeeName%");
        }

        if($customerName) {
            $query->where('user.full_name', 'LIKE', "%$customerName%");
        }

        // if ($sortByDateDesc === null) {
        //     $query->orderBy('transactions.created_at', 'DESC');
        // }

        // if ($sortByDateAsc === null) {
        //     $query->orderBy('transactions.created_at', 'ASC');
        // }

        $perPage ?: 15;

        $transaction = $query->paginate($perPage);

        return new TransactionCollection($transaction);
    }
}
