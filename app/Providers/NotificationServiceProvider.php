<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification_alert;
use Illuminate\Support\Facades\Auth;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.main', function ($view) {
            $notifications = Notification_alert::where('user_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            $view->with('notifications', $notifications);
        });
    }
}
