<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WriteOff extends Model {
    protected $fillable = [
        'status',
        'comment',
        'external',
        'store_number',
        'store',
        'date',
        'day_of_week',
        'total_weight',
        'total_amount',
        'total_detail',
        'counteragent',
        'contract_id',
        'contract',
        'retailer',
    ];

    protected $casts = [
        'total_detail' => 'array'
    ];

    public function contractDetail(): HasOne
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }
}
