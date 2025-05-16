<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share pending order count with all admin views
        View::composer('layouts.admin', function ($view) {
            $pendingOrderCount = Order::where('status', 'pending')->count();
            $view->with('pendingOrderCount', $pendingOrderCount);
        });
    }
}
