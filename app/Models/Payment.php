<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'send_to',
        'send_from',
        'payment_type',
        'payment_id',
        'method_type',
        'image_path',
        'amount',
        'status',
    ];
    
    public function sendTo() {
		return $this->belongsTo(User::class,'send_to','id');
	}

    public function sendFrom() {
		return $this->belongsTo(User::class,'send_from','id');
	}
}
