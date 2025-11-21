<?php

namespace App\Services\Worker;

use App\Models\Worker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate a worker and return token.
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws ValidationException
     */
    public function login(string $email, string $password): array
    {
        $worker = Worker::where('email', $email)->first();

        if (!$worker || !Hash::check($password, $worker->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        if ($worker->status !== 'active') {
             throw ValidationException::withMessages([
                'email' => ['Account is inactive'],
            ]);
        }

        $token = $worker->createToken('worker-token')->plainTextToken;

        return [
            'message' => 'Login successful',
            'token' => $token,
            'worker' => $worker
        ];
    }

    /**
     * Logout worker.
     *
     * @param \App\Models\Worker $worker
     * @return void
     */
    public function logout($worker): void
    {
        $worker->currentAccessToken()->delete();
    }
}
