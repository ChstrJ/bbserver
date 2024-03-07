<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    // protected $casts = ['is_remove' => 'boolean'];

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'quantity',
        'srp',
        'member_price',
        'is_remove'
    ];

    public function scopeActive ($query) {
        return $query->where('is_remove', false);
    }
}
