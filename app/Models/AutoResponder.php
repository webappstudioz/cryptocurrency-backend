<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoResponder extends Model
{
    public $timestamps = true;
    protected $fillable = [
		'id',
		'subject',
       	'template',
       	'template_name',
		'type',
       	'status',
	    'created_at',
	    'updated_at',
    ];
}
