<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id', 
        'phone_number',
        'created_at',
        'updated_at'
    ];
}
