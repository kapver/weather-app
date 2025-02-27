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
    /**
     * Generate the URL for fetching weather data
     *
     * @return string
     * @throws \Exception
     */
    public function getUrl(): string
    {
        $API_KEY = env('OPEN_WEATHER_API_KEY');

        if (empty($API_KEY)) {
            throw new \Exception('OPEN_WEATHER_API_KEY is empty');
        }

        $query_string = http_build_query([
            'lat'     => $this->latitude,
            'lon'     => $this->longitude,
            'appid'   => $API_KEY,
            'exclude' => 'current,minutely,daily', // alerts,hourly
            'units'   => 'metric',
            // 'mode' => 'json',
            'lang'    => 'en',
            // 'cnt' => 1,
        ]);

        // one call api
        return "https://api.openweathermap.org/data/3.0/onecall?$query_string";

        // another forecast way is 5 days forecast with 3 hours step
        // return "https://api.openweathermap.org/data/2.5/forecast?$query_string";
    }
}