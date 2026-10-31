<?php

namespace App\Providers;

use App\Http\Responses\SsoLogoutResponse;
use App\Models\Pegawai;
use App\Models\PeriodeTahun;
use App\Observers\PegawaiObserver;
use App\Observers\PeriodeTahunObserver;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (interface_exists(LogoutResponse::class)) {
            $this->app->singleton(LogoutResponse::class, SsoLogoutResponse::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        PeriodeTahun::observe(PeriodeTahunObserver::class);
        Pegawai::observe(PegawaiObserver::class);

        $this->app->resolving(Factory::class, function ($socialite, $app) {
            $socialite->extend('sipetra', function ($app) use ($socialite) {
                $config = $app['config']['services.sipetra'];

                return $socialite->buildProvider(SipetraSocialiteProvider::class, $config);
            });
        });
    }
}
