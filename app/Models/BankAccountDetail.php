<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccountDetail extends Model
{
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'account_holder_name',
        'upi_id',
        'account_image',
        'status',
        'created_at',
        'updated_at'
    ];
}
