<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InvoiceWriteOffs extends Model {
    public $timestamps = false;

    protected $fillable = [
        'invoice_id',
        'write_off_id',
    ];

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function writeOff(): HasOne
    {
        return $this->hasOne(WriteOff::class, 'id', 'write_off_id');
    }
}
