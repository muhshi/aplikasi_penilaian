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
        PeriodeTahun::observe(PeriodeTahunObserver::class);
    }
}
