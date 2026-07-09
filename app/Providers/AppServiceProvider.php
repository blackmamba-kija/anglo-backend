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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\User::observe(\App\Observers\ActivityObserver::class);
        \App\Models\Station::observe(\App\Observers\ActivityObserver::class);
        \App\Models\ConsumableItem::observe(\App\Observers\ActivityObserver::class);
        \App\Models\Asset::observe(\App\Observers\ActivityObserver::class);
        \App\Models\Transaction::observe(\App\Observers\ActivityObserver::class);
        \App\Models\InventoryRequest::observe(\App\Observers\ActivityObserver::class);
        \App\Models\LocalRecord::observe(\App\Observers\ActivityObserver::class);
    }
}
