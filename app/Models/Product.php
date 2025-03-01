<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = ['is_removed'];
    protected $fillable = [
        'category_id',
        'user_id',
        'name',
        'description',
        'quantity',
        'srp',
        'member_price',
        'product_code',
        'created_by',
        'updated_by'
    ];
    public function product() : BelongsToMany
    {
        return $this->belongsToMany(Transaction::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function setUser($value)
    {
        $this->attributes['user_id'] = Auth::user()->id;
    }
}
