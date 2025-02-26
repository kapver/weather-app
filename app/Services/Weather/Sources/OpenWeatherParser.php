<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

use Illuminate\Support\Facades\Log;

class OpenWeatherParser extends WeatherParser
{
    public function parsePop(): float
    {
        return (float) $this->parseValue('hourly.0.pop');
    }

    public function parseUvi(): float
    {
        return (float) $this->parseValue('hourly.0.uvi');
    }

    public function parseTemp(): float
    {
        return (float) $this->parseValue('hourly.0.temp');
    }

    public function parseTime(): int
    {
        return (int) $this->parseValue('hourly.0.dt');
    }

    public function parseType(): string
    {
        $first = $this->parseValue('hourly.0');
        $temp = $this->parseTemp();

        $hasSnowAttr = $first->snow ?? false;
        $hasRainAttr = $first->rain ?? false;

        return ($this->parsePop() > 0)
            ? $hasSnowAttr
                ? 'snow'
                : ($hasRainAttr ? 'rain' : '')
            : ($temp > 0 ? 'rain' : 'snow');
    }
}