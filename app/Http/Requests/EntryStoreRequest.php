<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntryStoreRequest extends FormRequest {
    public function rules(): array {
        return [
            'status' => 'required|string|max:255',
            'datetime' => 'required|date',
            'number' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'counteragent' => 'required|string|max:255',
            'counteragent_bank_account' => 'required|string|max:255',
            'contract' => 'required|string|max:255',
            'payment_purpose' => 'required|string|max:255',
            'operation_type' => 'required|string|max:255',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
