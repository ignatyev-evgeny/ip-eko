<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SberIntegration extends Model {
    protected $table = 'integration_sberbank';

    protected $fillable = [
        'id',
        'client_id',
        'client_secret',
        'scope',
        'access_token',
        'refresh_token',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];
}
