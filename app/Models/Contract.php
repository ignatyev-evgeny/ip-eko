<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model {
    protected $fillable = [
        'bitrix_id',
        'client_id',
        'client',
        'shop',
        'shop_address',
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

    public function getPriceAttribute($value)
    {
        preg_match('/\d+(\.\d{1,2})?/', $value, $matches);
        return (float) $value;
    }

    public function getPriceFruitsVegetablesAttribute($value)
    {
        return (float) $value;
    }

    public function getPriceBakeryAttribute($value)
    {
        return (float) $value;
    }

    public function getPriceDairyAttribute($value)
    {
        return (float) $value;
    }

    public function getPriceUsedOilAttribute($value)
    {
        return (float) $value;
    }

    public function getPriceGroceryAttribute($value)
    {
        return (float) $value;
    }

    public function getPriceWasteAttribute($value)
    {
        return (float) $value;
    }

    public function getOtherAttribute($value)
    {
        return (float) $value;
    }

    protected $casts = [
        'export_week_days' => 'array'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(ContractsBalanceHistory::class, 'contract_id', 'id');
    }

    public function counteragent(): HasMany
    {
        return $this->hasMany(ContractsBalanceHistory::class, 'contract_id', 'id');
    }

}
