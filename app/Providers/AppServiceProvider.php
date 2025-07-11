<?php

namespace App\Providers;

use App\Services\ExternalServerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar el ExternalServerService como singleton
        $this->app->singleton(ExternalServerService::class, function ($app) {
            return new ExternalServerService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
