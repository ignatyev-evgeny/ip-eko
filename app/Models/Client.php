<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    protected $fillable = [
        'bitrix_id',
        'name',
        'phone',
        'city',
    ];
}
