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
    protected $table = "transactions";

    
    protected $fillable = [
        'user_id',
        'amount_due',
        'number_of_items',
        'payment_type',
    ];

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
