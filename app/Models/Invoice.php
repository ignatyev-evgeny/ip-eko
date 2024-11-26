<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model {
    public $timestamps = false;

    protected $fillable = [
        'contract_id',
        'generated',
        'sent',
        'paid',
        'date_created',
        'date_generated',
        'date_sent',
        'date_paid',
    ];

    protected $casts = [
        'generated' => 'boolean',
        'sent' => 'boolean',
        'paid' => 'boolean',
        'date_created' => 'datetime',
        'date_generated' => 'datetime',
        'date_sent' => 'datetime',
        'date_paid' => 'datetime',
    ];

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }

    public function writeOffs(): HasMany
    {
        return $this->hasMany(InvoiceWriteOffs::class, 'invoice_id', 'id');
    }
}
