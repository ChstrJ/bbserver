<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = "users";
    protected $hidden = ['created_at', 'updated_at', 'password'];

    protected $fillable = [
        'full_name',
        'password',
        'is_active',
        'username',
        'last_login_at',
        'last_logout_at'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
