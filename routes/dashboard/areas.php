<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Admin\AreaWebController;
use App\Http\Controllers\Api\Admin\AreaController;

Route::middleware(['auth:sanctum'])->group(function () {

Route::prefix('areas')->group(function () {
    Route::get('/', [AreaWebController::class, 'index']); // Web View

    // API Routes (Restored)
    Route::post('/', [AreaController::class, 'store']);
    Route::get('/{id}', [AreaController::class, 'show']); // API Show
    Route::put('/{id}', [AreaController::class, 'update']);
    Route::delete('/{id}', [AreaController::class, 'destroy']);
    Route::post('/{id}/toggle', [AreaController::class, 'toggle']);
});

});