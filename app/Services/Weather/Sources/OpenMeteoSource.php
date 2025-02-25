<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

/**
 * Weather source from open-meteo.com
 *
 * @link https://open-meteo.com/en/docs
 */
class OpenMeteoSource extends WeatherSource
{
    protected function getUrl(): string
    {
        $query_string = http_build_query([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'hourly' => 'temperature_2m,precipitation_probability,precipitation,uv_index,uv_index_clear_sky',
            'wind_speed_unit' => 'ms',
            'timeformat' => 'unixtime',
            'timezone' => 'auto',
            'forecast_days' => '1',
            'forecast_hours' => '2',
        ]);

        return "https://api.open-meteo.com/v1/forecast?$query_string";
    }
}