<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Worker\AuthController;
use App\Http\Controllers\Api\Worker\OrderController;

/*
|--------------------------------------------------------------------------
| Worker API Routes
|--------------------------------------------------------------------------
|
| These routes are for worker functionality like login, viewing assigned
| orders, and updating order status.
|
*/

// Worker authentication
Route::post('/login', [AuthController::class, 'login']);

// Protected worker routes
Route::middleware(['auth:worker'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
