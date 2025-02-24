<?php

declare(strict_types=1);

namespace App\Services\Weather;

use App\Services\Settings;
use Illuminate\Support\Str;

class WeatherSettings extends Settings
{
    protected string $key = 'weather';

    /**
     * TODO need to investigate pop values meaning https://www.weather.gov/lmk/pops
     */
    public function getDefaults(): array
    {
        return [
            'alert_enabled' => config('weather.alert', false),
            'average_enabled' => config('weather.average', false),
            'pause_enabled' => config('weather.paused_until'),
            'pop_threshold' => config('weather.pop_threshold', 0.5), // probability of precipitation
            'uvi_threshold' => config('weather.uvi_threshold', 5),
            'email_enabled' => config('weather.email_enabled', true),
            'telegram_enabled' => config('weather.telegram_enabled', false),
            'telegram_verification_code' => (string) Str::ulid(),
            'telegram_chat_id' => null,
        ];
    }
}