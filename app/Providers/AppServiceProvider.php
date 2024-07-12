<?php

namespace App\Providers;

use Base\Custom\Illuminate\Database\Schema\Blueprint as AppBlueprint;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Blueprint::class, AppBlueprint::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
