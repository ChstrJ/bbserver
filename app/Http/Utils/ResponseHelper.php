<?php

namespace App\Http\Utils;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;

trait ResponseHelper
{
    public static function json($data, $code = 200): JsonResponse 
    {
        return response()->json($data, $code); 
    }
}
