<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        // Selama pengembangan, lazy loading relasi dianggap error.
        // Ini membuktikan tidak ada query N+1 di aplikasi (nonaktif di production).
        Model::preventLazyLoading(! $this->app->isProduction());

        // Data untuk ikon notifikasi di menu atas
        View::composer('components.notification-bell', function ($view) {
            $user = auth()->user();

            $view->with([
                'unreadCount' => $user ? $user->unreadNotifications()->count() : 0,
                'latestNotifications' => $user ? $user->notifications()->limit(5)->get() : collect(),
            ]);
        });
    }
}