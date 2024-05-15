<?php

namespace App\Models;

use App\Http\Utils\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'last_activity',
        'role_id',
        'status'
    ];

    public function appointments() : HasMany
    {   
        return $this->hasMany(Appointment::class);
    }
    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function products() : HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers() : HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function role() : BelongsTo
    {
        return $this->belongsTo(Role::class);
    }


    public function isOnline() : bool
    {                        
        return $this->last_activity >= now()->subMinutes(15);
    }

}
