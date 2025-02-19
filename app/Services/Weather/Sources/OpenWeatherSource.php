<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

/**
 * Weather source from openweathermap.org
 *
 * @link https://openweathermap.org/api/one-call-3
 */
class OpenWeatherSource extends WeatherSource
{
    protected function getUrl(): string
    {
        $API_KEY = env('OPEN_WEATHER_API_KEY');

        if (empty($API_KEY)) {
            throw new \Exception('API Key not set');
        }

        $query_string = http_build_query([
            'lat' => $this->latitude,
            'lon' => $this->longitude,
            'appid' => $API_KEY,
            'exclude' => 'current,minutely,daily', // alerts,hourly
            'units' => 'metric',
            // 'mode' => 'json',
            'lang' => 'en',
            // 'cnt' => 1,
        ]);

        // one call api
        return "https://api.openweathermap.org/data/3.0/onecall?$query_string";

        // another forecast way is 5 days forecast with 3 hours step
        // return "https://api.openweathermap.org/data/2.5/forecast?$query_string";
    }

    protected function parsePop(): float
    {
        return (float) $this->parseValue('hourly.0.pop');
    }

    protected function parseUvi(): float
    {
        return (float) $this->parseValue('hourly.0.uvi');
    }

    protected function parseTemp(): float
    {
        return (float) $this->parseValue('hourly.0.temp');
    }

    protected function parseTime(): int
    {
        return (int) $this->parseValue('hourly.0.dt');
    }

    protected function parseType(): string
    {
        $first = $this->parseValue('hourly.0');
        return ($this->parsePop() > 0)
            ? $first->snow
                ? 'snow'
                : ($first->rain ? 'rain' : '')
            : '';
    }
}