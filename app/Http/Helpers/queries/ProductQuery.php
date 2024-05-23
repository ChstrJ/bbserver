<?php

namespace App\Http\Helpers\queries;

use App\Http\Helpers\product\ProductStatus;
use App\Models\Product;

class ProductQuery
{
    public static function ProductQuery()
    {
        return Product::query()
            ->whereNot('is_removed', ProductStatus::$REMOVE)
            ->orderBy('created_at', 'DESC')
            ->orderBy('updated_at', 'DESC');
    }

    public static function applyFilters($query, $request)
    {
        self::applySearch($query, $request->input('search'));
        self::applySortBy($query, $request->input('sort_by_desc'), $request->input('sort_by_asc'));
        self::applyPerPage($query, $request->input('per_page'));
        self::filterByCategory($query, $request->input('category_id'));
    }

    private static function filterByCategory($query, $categoryId)
    {
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
    }

    private static function applySortBy($query, $sortByDesc, $sortByAsc)
{
    if ($sortByDesc) {
        $query->orderBy($sortByDesc, 'DESC');
    }

    if ($sortByAsc) {
        $query->orderBy($sortByAsc, 'ASC');
    }
}
    private static function applyPerPage($query, $perPage = 15)
    {
        if ($perPage) {
            $query->paginate($perPage);
        }
    }

    private static function applySearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('srp', 'LIKE', "%$search%")
                    ->orWhere('member_price', 'LIKE', "%{$search}%");
            });
        }
    }
}