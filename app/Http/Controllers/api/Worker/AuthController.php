<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $worker = Worker::where('email', $request->email)->first();

        if (!$worker || !Hash::check($request->password, $worker->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if ($worker->status !== 'active') {
            return response()->json(['error' => 'Account is inactive'], 403);
        }

        $token = $worker->createToken('worker-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'worker' => $worker
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
