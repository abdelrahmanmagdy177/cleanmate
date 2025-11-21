<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workerId = $this->route('id');
        
        return [
            'name' => 'string',
            'email' => 'email|unique:workers,email,' . $workerId,
            'phone' => 'string',
            'password' => 'string|min:6',
            'status' => 'in:active,inactive',
        ];
    }
}
