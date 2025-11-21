<?php

namespace App\Http\Controllers\api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\LoginRequest;
use App\Http\Requests\Customer\RegisterRequest;
use App\Services\Customer\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->register($data);

        return response()->json($result, 201);
    }

    public function login(LoginRequest $request)
    {
        $tokenData = $this->authService->login(
            $request->input('login'),
            $request->input('password')
        );

        return response()->json($tokenData);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
