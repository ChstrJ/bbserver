<?php

namespace App\Models;

use App\Http\Utils\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = "users";
    protected $hidden = ['password', 'is_active'];

    protected $fillable = [
        'full_name',
        'password',
        'is_active',
        'username',
        'last_login_at',
        'last_logout_at',
        'role_id'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function customers() {
        return $this->hasMany(Customer::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin() {
        return $this->role_id == Roles::$ADMIN;
    }

    public function isEmployee() {
        return $this->role_id == Roles::$EMPLOYEE;
    }
}
