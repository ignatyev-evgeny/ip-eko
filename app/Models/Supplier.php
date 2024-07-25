<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $fillable = [
        'bitrix_id',
        'title',
        'email',
        'retailer',
        'address',
        'internal_number',
        'external_number',
        'region',
        'cities',
        'direction',
        'type_point',
        'type_payment',
        'price',
        'contract_with',
        'legal_name',
        'problem',
        'export_frequency',
        'export_days',
        'supplier_status',
        'supplier_tech_status',
        'supplier_free_days',
        'graph_id',
        'price_filter',
    ];

    protected $casts = [
        'cities' => 'array',
        'export_days' => 'array',
        'supplier_free_days' => 'array',
    ];
}
