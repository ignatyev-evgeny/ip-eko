<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WriteOff extends Model {
    protected $fillable = [
        'external',
        'store',
        'date',
        'total_weight',
        'total_amount',
        'total_detail',
        'counteragent',
        'contract',
        'retailer',
    ];

    protected $casts = [
        'total_detail' => 'array'
    ];
}
