<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

class OpenWeatherParser extends WeatherParser
{
    /**
     * Parse precipitation probability.
     *
     * @return float
     */
    public function parsePop(): float
    {
        return (float)$this->parseValue('hourly.0.pop');
    }

    /**
     * Parse ultraviolet index.
     *
     * @return float
     */
    public function parseUvi(): float
    {
        return (float)$this->parseValue('hourly.0.uvi');
    }

    /**
     * Parse temperature.
     *
     * @return float
     */
    public function parseTemp(): float
    {
        return (float)$this->parseValue('hourly.0.temp');
    }

    /**
     * Parse timestamp.
     *
     * @return int
     */
    public function parseTime(): int
    {
        return (int)$this->parseValue('hourly.0.dt');
    }

    /**
     * Determine weather type based on attributes.
     *
     * @return string
     */
    public function parseType(): string
    {
        $first = $this->parseValue('hourly.0');
        $temp  = $this->parseTemp();

        $hasSnowAttr = $first->snow ?? false;
        $hasRainAttr = $first->rain ?? false;

        return ($this->parsePop() > 0)
            ? $hasSnowAttr
                ? 'snow'
                : ($hasRainAttr ? 'rain' : '')
            : ($temp > 0 ? 'rain' : 'snow');
    }
}