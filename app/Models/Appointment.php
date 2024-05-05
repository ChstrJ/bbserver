<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name' ,
        'description',
        'start_time',
        'end_time',
        'date',
        'status'
    ];

    public function customer()
    {
        $this->belongsTo(Customer::class);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
}


