<?php

namespace App\Http\Helpers\queries;

use App\Http\Helpers\user\UserService;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionQuery
{

    public static function TransactionQuery()
    {
        return Transaction::query()
            ->select('transactions.*')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->where('transactions.user_id', UserService::getUserId())
            ->orderBy('transactions.created_at', 'DESC')
            ->orderBy('transactions.status', 'ASC')
            ->with('customer', 'user');
    }
    public static function applyFilters($query, Request $request)
    {
        self::filterByDate($query, $request->input('start_date'), $request->input('end_date'));
        self::filterByCategory($query, $request->input('category_id'));
        self::filterByStatus($query, $request->input('status'));
        self::applySorting($query, $request->input('sort_by_desc'), $request->input('sort_by_asc'));
        self::applySearch($query, $request->input('search'));
        self::applyPerPage($query, $request->input('per_page'));
    }

    private static function applyPerPage($query, $perPage = 20)
    {
        if ($perPage) {
            $query->paginate($perPage);
        }
    }

    private static function filterByDate($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('transactions.created_at', '<=', $endDate);
        }
    }

    private static function filterByCategory($query, $categoryId)
    {
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
    }

    private static function filterByStatus($query, $status)
    {
        if ($status) {
            $query->where('transactions.status', $status);
        }
    }

    private static function applySorting($query, $sortByDesc, $sortByAsc)
    {
        if ($sortByDesc) {
            $query->orderBy("transactions.$sortByDesc", 'DESC');
        }
        if ($sortByAsc) {
            $query->orderBy("transactions.$sortByAsc", 'ASC');
        }
    }

    private static function applySearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transactions.reference_number', 'LIKE', "%{$search}%")
                    ->orWhere('customers.name', 'LIKE', "%{$search}%");
            });
        }
    }
}
