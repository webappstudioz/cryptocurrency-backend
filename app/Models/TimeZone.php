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

    public function result() {
		return $this->hasOne(StoreTimeZone::class)->where('date',date('Y-m-d'));
	}
}
