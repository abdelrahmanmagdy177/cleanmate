<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['layouts.navigation', 'layouts.sidebar'], function ($view) {
            $sidebarService = new \App\Services\Dashboard\SidebarService();
            $view->with('menuItems', $sidebarService->getMenuItems());
        });
    }
}
