<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'quantity',
        'srp',
        'member_price',
        'is_remove'
    ];
}
