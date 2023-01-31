<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\Brand::observe(\App\Observers\BrandObserver::class);
        \App\Models\Mark::observe(\App\Observers\MarkObserver::class);
        \App\Models\CarFixedPrice::observe(\App\Observers\FixedCarPrice::class);
        \App\Models\Complectation::observe(\App\Observers\ComplectationChangePriceObserver::class);
        \App\Models\CarMarker::observe(\App\Observers\CarMarkerObserver::class);
        \App\Models\Trafic::observe(\App\Observers\TraficObserver::class);
        \App\Models\Worksheet::observe(\App\Observers\WorksheetObserver::class);
    }
}
