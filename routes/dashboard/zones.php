<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Admin\ZoneWebController;
use App\Http\Controllers\Api\Admin\ZoneController;

Route::middleware(['auth:sanctum'])->group(function () {

Route::prefix('zones')->group(function () {
    Route::get('/', [ZoneWebController::class, 'index']); // Web View

    // API Routes (Restored)
    Route::post('/', [ZoneController::class, 'store']);
    Route::get('/{id}', [ZoneController::class, 'show']); // API Show
    Route::put('/{id}', [ZoneController::class, 'update']);
    Route::delete('/{id}', [ZoneController::class, 'destroy']);
    Route::post('/{id}/toggle', [ZoneController::class, 'toggle']);
});
});