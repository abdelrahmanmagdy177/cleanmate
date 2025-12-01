<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Admin\OrderController;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('dashboard.admin.orders.index');
    });

});
