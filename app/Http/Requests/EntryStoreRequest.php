<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntryStoreRequest extends FormRequest {
    public function rules(): array {
        return [
            'status' => 'required|string|max:255',
            'datetime' => 'required|date',
            'number' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
            'counteragent' => 'nullable|string|max:255',
            'counteragent_bank_account' => 'nullable|string|max:255',
            'contract' => 'nullable|string|max:255',
            'payment_purpose' => 'required|string|max:255',
            'operation_type' => 'required|string|max:255',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
