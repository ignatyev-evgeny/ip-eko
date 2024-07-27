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
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
