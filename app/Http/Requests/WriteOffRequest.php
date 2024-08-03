<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WriteOffRequest extends FormRequest {
    public function rules(): array {
        return [
            'external' => 'nullable|string|max:255',
            'store' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'total_weight' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'counteragent' => 'nullable|string|max:255',
            'contract' => 'nullable|string|max:255',
            'retailer' => 'nullable|string|max:255',
            'fruits_amount' => 'nullable|numeric',
            'fruits_weight' => 'nullable|numeric',
            'bread_amount' => 'nullable|numeric',
            'bread_weight' => 'nullable|numeric',
            'milk_amount' => 'nullable|numeric',
            'milk_weight' => 'nullable|numeric',
            'food_waste_amount' => 'nullable|numeric',
            'food_waste_weight' => 'nullable|numeric',
            'used_vegetable_oil_amount' => 'nullable|numeric',
            'used_vegetable_oil_weight' => 'nullable|numeric',
            'groceries_amount' => 'nullable|numeric',
            'groceries_weight' => 'nullable|numeric',
            'other_amount' => 'nullable|numeric',
            'other_weight' => 'nullable|numeric',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
