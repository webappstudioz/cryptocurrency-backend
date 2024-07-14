<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceRejectionReason extends Model
{
    protected $fillable = [
        'id',
        'reason',
    ];
}
