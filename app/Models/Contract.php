<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model {
    protected $fillable = [
        'bitrix_id',
        'client_id',
        'supplier_id',
        'title',
        'balance',
        'recommended_payment',
        'previous_period_amount',
        'type',
        'number',
        'date',
        'phone',
        'create_deals',
        'export_start_date',
        'export_week_days',
        'export_frequency',
        'payment_total',
        'payment_type',
        'export_total_count',
        'attorney_date',
        'price',
        'price_fruits_vegetables',
        'price_bakery',
        'price_dairy',
        'price_used_oil',
        'price_grocery',
        'price_waste',
        'other',
        'city',
        'status',
        'shipment',
        'process',
        'supplier_registered',
        'retailer',
        'region',
        'balance_status',
        'source',
    ];
}
