<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\CustomerAddressController;
use App\Http\Controllers\Api\Customer\AuthController;
use App\Http\Controllers\Api\Customer\NotificationController;
use App\Http\Controllers\Api\Customer\ZoneController;
use App\Http\Controllers\Api\Customer\AreaController;
use App\Http\Controllers\Api\Customer\ProfileController;
/*
|--------------------------------------------------------------------------
| Customer API Routes
|--------------------------------------------------------------------------
|
| These routes are for customer-facing functionality like placing orders,
| managing addresses, and viewing available timeslots.
|
*/

// Public routes

Route::post("login",[AuthController::class,'login']);
Route::post("register",[AuthController::class,'register']);

Route::middleware('auth:sanctum')->group(function () { 
Route::post("logout",[AuthController::class,'logout']); 
Route::get('/timeslots', [OrderController::class, 'getTimeslots']);
Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/customer-addresses', [CustomerAddressController::class, 'index']);
    Route::post('/customer-addresses', [CustomerAddressController::class, 'store']);
    Route::put('/customer-addresses/{id}', [CustomerAddressController::class, 'update']);
    Route::delete('/customer-addresses/{id}', [CustomerAddressController::class, 'destroy']);

    // Customer notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Zones and areas (for address selection)
    Route::get('/zones', [ZoneController::class, 'index']);
    Route::get('/areas', [AreaController::class, 'index']);
    
    // Update customer area
    Route::post('/area', [ProfileController::class, 'updateArea']);

    // Services
    Route::get('/services', [App\Http\Controllers\Api\ServiceController::class, 'index']);
    Route::get('/services/{id}', [App\Http\Controllers\Api\ServiceController::class, 'show']);

    // Shopping Cart
    Route::get('/cart', [App\Http\Controllers\Api\Customer\CartController::class, 'index']);
    Route::post('/cart', [App\Http\Controllers\Api\Customer\CartController::class, 'store']);
    Route::put('/cart/{id}', [App\Http\Controllers\Api\Customer\CartController::class, 'update']);
    Route::delete('/cart/{id}', [App\Http\Controllers\Api\Customer\CartController::class, 'destroy']);
    Route::delete('/cart', [App\Http\Controllers\Api\Customer\CartController::class, 'clear']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/processing', [OrderController::class, 'processing']);
    Route::get('/orders/finished', [OrderController::class, 'finished']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});



