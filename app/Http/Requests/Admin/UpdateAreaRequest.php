<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'zone_id' => 'sometimes|exists:zones,id',
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
