<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Weather\Sources\OpenWeatherSource;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

use App\Services\Weather\WeatherService;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
