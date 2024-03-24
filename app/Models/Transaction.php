<?php

namespace App\Models;

use App\Http\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'transactions';
    protected $casts = ['checkouts' => 'array', 'customer' => 'array'];
    protected $fillable = [
        'user_id',
        'customer_id',
        'amount_due',
        'number_of_items',
        'payment_method',
        'checkouts',
        'customer',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
