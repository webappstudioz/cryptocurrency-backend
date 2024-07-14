<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'city',
        'zip_code',
        'wallet_amount',
        'status',
        'created_at',
        'updated_at'
    ];
}
