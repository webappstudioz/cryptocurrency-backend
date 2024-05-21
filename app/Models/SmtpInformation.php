<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpInformation extends Model
{
    protected $fillable = [
        'host',
        'port',
        'from_email',
        'from_name',
        'username',
        'password',
        'encryption',
        'status',
        'created_at',
        'updated_at',
    ];
}
