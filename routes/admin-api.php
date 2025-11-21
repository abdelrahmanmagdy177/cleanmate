<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\WorkerController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| These routes are for admin/dashboard functionality like managing workers,
| assigning orders, and viewing system data.
|
*/

// Worker management
Route::get('/workers', [WorkerController::class, 'index']);
Route::post('/workers', [WorkerController::class, 'store']);
Route::put('/workers/{id}', [WorkerController::class, 'update']);
Route::delete('/workers/{id}', [WorkerController::class, 'destroy']);

// Order management
Route::post('/orders/{id}/assign', [WorkerController::class, 'assignOrder']);

// Zone management
Route::get('/zones', [App\Http\Controllers\Api\Admin\ZoneController::class, 'index']);
Route::post('/zones', [App\Http\Controllers\Api\Admin\ZoneController::class, 'store']);
Route::get('/zones/{id}', [App\Http\Controllers\Api\Admin\ZoneController::class, 'show']);
Route::put('/zones/{id}', [App\Http\Controllers\Api\Admin\ZoneController::class, 'update']);
Route::delete('/zones/{id}', [App\Http\Controllers\Api\Admin\ZoneController::class, 'destroy']);
Route::post('/zones/{id}/toggle', [App\Http\Controllers\Api\Admin\ZoneController::class, 'toggle']);

// Area management
Route::get('/areas', [App\Http\Controllers\Api\Admin\AreaController::class, 'index']);
Route::post('/areas', [App\Http\Controllers\Api\Admin\AreaController::class, 'store']);
Route::get('/areas/{id}', [App\Http\Controllers\Api\Admin\AreaController::class, 'show']);
Route::put('/areas/{id}', [App\Http\Controllers\Api\Admin\AreaController::class, 'update']);
Route::delete('/areas/{id}', [App\Http\Controllers\Api\Admin\AreaController::class, 'destroy']);
Route::post('/areas/{id}/toggle', [App\Http\Controllers\Api\Admin\AreaController::class, 'toggle']);
