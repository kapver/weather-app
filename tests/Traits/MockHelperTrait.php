<?php

namespace Tests\Traits;

use App\Services\Weather\Sources\OpenMeteoSource;
use App\Services\Weather\Sources\OpenWeatherSource;

trait MockHelperTrait {

    protected array $cityArgs = [
        'latitude'  => 18.483402,
        'longitude' => -69.929611,
        'city'      => 'Santo Domingo',
    ];

    protected array $validationKeys = [
        OpenWeatherSource::class => ['hourly.0.dt', 'hourly.0.temp',
            'hourly.0.uvi', 'hourly.0.pop'],
        OpenMeteoSource::class => ['hourly.time', 'hourly.temperature_2m',
            'hourly.uv_index', 'hourly.precipitation_probability'],
    ];

    protected array $userSettings = [
        "alert_enabled" => true,
        "average_enabled" => true,
        "pause_enabled" => null,
        'pop_threshold' => -1, // default test value to be sure condition value pass threshold
        'uvi_threshold' => -1,
        'email_enabled' => true,
        'telegram_enabled' => false,
        'telegram_verification_code' => '',
        'telegram_chat_id' => null,
    ];

}