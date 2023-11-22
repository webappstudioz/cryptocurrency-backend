<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    protected $fillable = [
        'name', 
        'time_zone',
        'winning_number',
        'created_at',
        'updated_at'
    ];
}
