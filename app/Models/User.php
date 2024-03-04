<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory;

    protected $table = "users";
    protected $casts = ['is_active' => 'boolean'];
    protected $hidden = ['created_at', 'updated_at', 'password'];

    protected $fillable = [
        'full_name',
        'username',
        'password',
        'is_active',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
