<?php

declare(strict_types=1);

namespace App\Services\Weather;

use App\Services\Settings;

class WeatherSettings extends Settings
{
    protected string $key = 'weather';

    public function getDefaults(): array
    {
        return [
            'alert_enabled' => config('weather.alert', false),
            'average_enabled' => config('weather.average', false),
            'pause_enabled' => config('weather.paused_until', false),
            'precipitation' => config('weather.precipitation', 10),
            'uvi' => config('weather.uvi', 5),
        ];
    }
}