<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \URL::forceScheme('https');
        try {
            $settings = Configuration::first();
            config(['webpush.vapid.public_key' => $settings->vapidSite]);
            config(['webpush.vapid.private_key' => $settings->vapidSecret]);
        } catch (\Throwable $th) {}
    }
}
