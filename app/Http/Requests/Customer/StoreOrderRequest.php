<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'variant_id' => 'required|exists:service_variants,id',
            'timeslot_id' => 'required|exists:timeslots,id',
            'customer_address_id' => 'required|exists:customer_addresses,id',
            'space' => 'required|integer|min:1',
            'order_date' => 'required|date|after_or_equal:today',
            'payment_method' => 'required|in:cash,credit',
            'notes' => 'nullable|string',
        ];
    }
}
