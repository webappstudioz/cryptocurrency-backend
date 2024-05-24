<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoAccountDetail extends Model
{
    protected $fillable = [
        'user_id',
        'crypto_id',
        'crypto_image',
        'status',
        'created_at',
        'updated_at'
    ];
}
