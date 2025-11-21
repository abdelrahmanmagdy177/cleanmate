<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'variant_id' => 'required|exists:service_variants,id',
            'customer_address_id' => 'required|exists:customer_addresses,id',
            'space' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }
}
