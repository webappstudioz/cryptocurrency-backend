<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberWinning extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'w_number',
        'timezone',
    ];
}
