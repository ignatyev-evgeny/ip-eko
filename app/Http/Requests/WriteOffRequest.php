<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WriteOffRequest extends FormRequest {
    public function rules(): array {
        return [
            'external' => 'nullable|string|max:255',
            'store_number' => 'nullable|string|max:255',
            'store' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'total_weight' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'counteragent' => 'nullable|string|max:255',
            'contract' => 'nullable|string|max:255',
            'retailer' => 'nullable|string|max:255',
            'fruits_price' => 'nullable|numeric',
            'fruits_weight' => 'nullable|numeric',
            'bread_price' => 'nullable|numeric',
            'bread_weight' => 'nullable|numeric',
            'milk_price' => 'nullable|numeric',
            'milk_weight' => 'nullable|numeric',
            'food_waste_price' => 'nullable|numeric',
            'food_waste_weight' => 'nullable|numeric',
            'used_vegetable_oil_price' => 'nullable|numeric',
            'used_vegetable_oil_weight' => 'nullable|numeric',
            'groceries_price' => 'nullable|numeric',
            'groceries_weight' => 'nullable|numeric',
            'other_price' => 'nullable|numeric',
            'other_weight' => 'nullable|numeric',
            'take_contract' => 'nullable|string',
            'detail_view' => 'nullable|string',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
