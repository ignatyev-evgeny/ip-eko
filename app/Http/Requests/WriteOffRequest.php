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

    public function messages(): array {
        return [
            'external.string' => 'Поле "External" должно быть строкой.',
            'external.max' => 'Поле "External" не должно превышать 255 символов.',
            'store_number.string' => 'Поле "Номер магазина" должно быть строкой.',
            'store_number.max' => 'Поле "Номер магазина" не должно превышать 255 символов.',
            'store.string' => 'Поле "Магазин" должно быть строкой.',
            'store.max' => 'Поле "Магазин" не должно превышать 255 символов.',
            'date.date' => 'Поле "Дата" должно быть валидной датой.',
            'total_weight.numeric' => 'Поле "Общий вес" должно быть числом.',
            'total_amount.numeric' => 'Поле "Общая сумма" должно быть числом.',
            'counteragent.string' => 'Поле "Контрагент" должно быть строкой.',
            'counteragent.max' => 'Поле "Контрагент" не должно превышать 255 символов.',
            'contract.string' => 'Поле "Договор" должно быть строкой.',
            'contract.max' => 'Поле "Договор" не должно превышать 255 символов.',
            'retailer.string' => 'Поле "Ритейлер" должно быть строкой.',
            'retailer.max' => 'Поле "Ритейлер" не должно превышать 255 символов.',
            'fruits_price.numeric' => 'Поле "Цена фруктов" должно быть числом.',
            'fruits_weight.numeric' => 'Поле "Вес фруктов" должно быть числом.',
            'bread_price.numeric' => 'Поле "Цена хлеба" должно быть числом.',
            'bread_weight.numeric' => 'Поле "Вес хлеба" должно быть числом.',
            'milk_price.numeric' => 'Поле "Цена молока" должно быть числом.',
            'milk_weight.numeric' => 'Поле "Вес молока" должно быть числом.',
            'food_waste_price.numeric' => 'Поле "Цена пищевых отходов" должно быть числом.',
            'food_waste_weight.numeric' => 'Поле "Вес пищевых отходов" должно быть числом.',
            'used_vegetable_oil_price.numeric' => 'Поле "Цена использованного масла" должно быть числом.',
            'used_vegetable_oil_weight.numeric' => 'Поле "Вес использованного масла" должно быть числом.',
            'groceries_price.numeric' => 'Поле "Цена бакалеи" должно быть числом.',
            'groceries_weight.numeric' => 'Поле "Вес бакалеи" должно быть числом.',
            'other_price.numeric' => 'Поле "Прочая цена" должно быть числом.',
            'other_weight.numeric' => 'Поле "Прочий вес" должно быть числом.',
            'take_contract.string' => 'Поле "Взять договор" должно быть строкой.',
            'detail_view.string' => 'Поле "Детальный вид" должно быть строкой.',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
