<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Admin\WorkerWebController;
use App\Http\Controllers\Api\Admin\WorkerController;

Route::middleware(['auth'])->group(function () {

Route::prefix('workers')->group(function () {
    Route::get('/', [WorkerWebController::class, 'index']); // Web View
    
    // API Routes (Restored)
    Route::post('/', [WorkerController::class, 'store']);
    Route::put('/{id}', [WorkerController::class, 'update']);
    Route::delete('/{id}', [WorkerController::class, 'destroy']);
    Route::post('/{id}/assign', [WorkerController::class, 'assignOrder']);
});
});