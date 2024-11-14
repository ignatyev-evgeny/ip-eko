<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractsBalanceHistory extends Model {
    protected $fillable = [
        'type',
        'type_relation',
        'contract_id',
        'start_balance',
        'amount',
        'end_balance',
        'comment',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'id', 'contract_id');
    }

}
