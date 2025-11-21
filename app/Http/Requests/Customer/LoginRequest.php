<?php
namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all users to attempt login
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // email or mobile input
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Optional: Customize error messages.
     */
    public function messages(): array
    {
        return [
            'login.required' => 'Please provide your email or mobile number.',
            'password.required' => 'Please provide your password.',
        ];
    }
}
