<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    protected $casts = ['is_remove' => 'boolean'];

    protected $fillable = [
        'user_id', 
        'category_id',
        'name',
        'description',
        'quantity',
        'srp',
        'member_price',
        'is_remove'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
