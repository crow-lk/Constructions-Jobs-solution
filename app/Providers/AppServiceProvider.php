<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

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
        // Set the application name to "Home Builders"
        Config::set('app.name', 'Home Builders');
        
        // Set the application logo path
        Config::set('app.logo', '/images/logoo.png');
        
        // Set the application tagline
        Config::set('app.tagline', 'FROM LAND TO LIVING');
    }
}
