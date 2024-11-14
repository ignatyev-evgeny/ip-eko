<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model {
    protected $fillable = [
        'uid',
        'status',
        'datetime',
        'number',
        'amount',
        'counteragent',
        'counteragent_bank_account',
        'contract_id',
        'contract',
        'payment_purpose',
        'operation_type',
    ];
}
