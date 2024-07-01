<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        \App\Models\Trafic::observe(\App\Observers\TraficObserver::class);
        \App\Models\Worksheet::observe(\App\Observers\WorksheetObserver::class);
        \App\Models\Role::observe(\App\Observers\RoleObserver::class);
        \App\Models\ClientEvent::observe(\App\Observers\ClientEventObserver::class);
        \App\Models\Car::observe(\App\Observers\CarObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        Validator::excludeUnvalidatedArrayKeys();
    }
}
