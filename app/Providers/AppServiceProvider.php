<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\PeriodeTahun;
use App\Observers\PeriodeTahunObserver;

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
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        \App\Models\PeriodeTahun::observe(\App\Observers\PeriodeTahunObserver::class);

        $socialite = $this->app->make(\Laravel\Socialite\Contracts\Factory::class);
        $socialite->extend('sipetra', function ($app) use ($socialite) {
            $config = $app['config']['services.sipetra'];
            return $socialite->buildProvider(\App\Providers\SipetraSocialiteProvider::class, $config);
        });
    }
}

