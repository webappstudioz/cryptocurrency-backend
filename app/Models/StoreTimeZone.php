<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreTimeZone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'time_zone_id',
        'winning_number',
        'date',
        'user_id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
