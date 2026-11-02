<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
    }
}