<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// User authentication routes
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Service routes (public)
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Customer API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->group(function () {
    require __DIR__.'/customer-api.php';
});

/*
|--------------------------------------------------------------------------
| Worker API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('worker')->group(function () {
    require __DIR__.'/worker-api.php';
});

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    require __DIR__.'/admin-api.php';
});
