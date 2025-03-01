<?php

namespace App\Models;

use App\Http\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'transactions';
    protected $timezone = 'Asia/Manila';
    protected $casts = ['checkouts' => 'json'];
    protected $fillable = [
        'reference_number',
        'user_id',
        'customer_id',
        'amount_due',
        'number_of_items',
        'payment_method',
        'checkouts',
        'status',
        'commission',
        'image'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    
}
