<?php

namespace App\Services\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user and create API token.
     *
     * @param  array  $data
     * @return array
     */
    public function register(array $data): array
    {
        // Create the customer
        $user = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['mobile'] ?? null, // Map mobile to phone
            'area_id' => $data['area'] ?? null, // Map area to area_id
            'password' => bcrypt($data['password']),
        ]);
        $token = $user->createToken('customer-api-token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Authenticate a user using email or mobile and return token.
     *
     * @param  string  $login    Email or mobile
     * @param  string  $password
     * @return array
     *
     * @throws ValidationException
     */
    public function login(string $login, string $password): array
    {
        // Find customer by email or phone
        $user = Customer::where('email', $login)
                    ->orWhere('phone', $login)
                    ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('customer-api-token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }

    /**
     * Logout user by deleting current access token.
     * (Optional - often handled in controller)
     *
     * @param  \Illuminate\Foundation\Auth\User  $user
     * @return void
     */
    public function logout($user): void
    {
        $user->currentAccessToken()->delete();
    }
}
