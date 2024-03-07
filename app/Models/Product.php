<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    // protected $casts = ['is_removed' => 'boolean'];

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'quantity',
        'srp',
        'member_price',
        'is_removed'
    ];

    public function scopeActive ($query) {
        return $query->where('is_removed', false);
    }
}
