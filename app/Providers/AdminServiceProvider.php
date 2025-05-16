<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\ContactMessage;
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
        // Share pending order count and unread contact messages count with all admin views
        View::composer('layouts.admin', function ($view) {
            $pendingOrderCount = Order::where('status', 'pending')->count();
            $unreadContactCount = ContactMessage::where('is_read', false)->count();

            $view->with([
                'pendingOrderCount' => $pendingOrderCount,
                'unreadContactCount' => $unreadContactCount
            ]);
        });
    }
}
