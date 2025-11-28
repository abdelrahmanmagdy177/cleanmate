<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| These routes are for the dashboard functionality.
| Routes are dynamically loaded from the routes/dashboard directory.
|
*/

// Admin Module
Route::prefix('admin')->group(function () {
    
    $path = base_path('routes/dashboard');
    
    if (File::exists($path)) {
        $files = File::files($path);
        foreach ($files as $file) {
            require $file->getPathname();
        }
    }

});

// Worker Module (Placeholder)
Route::prefix('worker')->group(function () {
    // Worker specific routes can go here
});
