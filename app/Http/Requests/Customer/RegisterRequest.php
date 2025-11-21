<?php
namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow everyone
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'], // requires password_confirmation
            'mobile' => ['nullable', 'string', 'max:20'],
            'area' => ['nullable', 'integer', 'exists:areas,id'],
        ];
    }
}
