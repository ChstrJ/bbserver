<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'phone_number',
        'address',
        'email_address',
        'created_by',
        'updated_by',
        'is_active'
    ];

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function appointment() : HasMany 
    {
        return $this->hasMany(Appointment::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
