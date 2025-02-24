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

    protected function parsePop(): float
    {
        $value = (float) last($this->parseValue('hourly.precipitation_probability'));

        // Convert percents 5% to decimal 0.05
        if ($value > 1) {
            $value = $value / 100;
        }

        return $value;
    }

    protected function parseUvi(): float
    {
        return (float) last($this->parseValue('hourly.uv_index'));
    }

    protected function parseTemp(): float
    {
        return (float) last($this->parseValue('hourly.temperature_2m'));
    }

    protected function parseTime(): int
    {
        return (int) last($this->parseValue('hourly.time'));
    }

    protected function parseType(): string
    {
        return $this->parseTemp() >= 0
            ? 'rain'
            : 'snow';
    }
}