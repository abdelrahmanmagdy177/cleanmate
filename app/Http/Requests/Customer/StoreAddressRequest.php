<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_email' => 'required|email',
            'name' => 'required|string',
            'title' => 'required|string',
            'address_details' => 'required|string',
            'is_default' => 'boolean',
        ];
    }
}
