<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'zone_id' => 'required|exists:zones,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}
