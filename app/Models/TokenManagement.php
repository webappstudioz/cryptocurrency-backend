<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenManagement extends Model
{
    protected $fillable = [
		'id',
		'email',
       	'token',
       	'otp',
	    'created_at',
	    'updated_at',
    ];
}
