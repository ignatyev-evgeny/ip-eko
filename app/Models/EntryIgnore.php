<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryIgnore extends Model {
    public $timestamps = false;

    protected $fillable = [
        'counteragent',
        'counteragent_bank_account',
    ];
}
