<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WriteOffRequest extends FormRequest {
    public function rules(): array {
        return [
            'external' => 'required|string|max:255',
            'store' => 'required|string|max:255',
            'date' => 'required|date',
            'total_weight' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'counteragent' => 'required|string|max:255',
            'contract' => 'required|string|max:255',
            'retailer' => 'required|string|max:255',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
