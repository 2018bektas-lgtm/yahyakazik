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
        // Site ayarları istek boyunca bir kez okunsun
        $this->app->singleton(\App\Support\SiteAyarlari::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Panelden değiştirilen büro bilgileri config('site') değerlerinin yerine geçer
        $this->app->make(\App\Support\SiteAyarlari::class)->siteBilgileriniUygula();
    }
}
