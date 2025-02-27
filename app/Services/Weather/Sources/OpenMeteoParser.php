<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

class OpenMeteoParser extends WeatherParser
{
    /**
     * Parse precipitation probability.
     *
     * @return float
     */
    public function parsePop(): float
    {
        $value = (float)last($this->parseValue('hourly.precipitation_probability'));

        // Convert percents 5% to decimal 0.05
        if ($value > 1) {
            $value = $value / 100;
        }

        return $value;
    }

    /**
     * Parse ultraviolet index.
     *
     * @return float
     */
    public function parseUvi(): float
    {
        return (float)last($this->parseValue('hourly.uv_index'));
    }

    /**
     * Parse temperature.
     *
     * @return float
     */
    public function parseTemp(): float
    {
        return (float)last($this->parseValue('hourly.temperature_2m'));
    }

    /**
     * Parse timestamp.
     *
     * @return int
     */
    public function parseTime(): int
    {
        return (int)last($this->parseValue('hourly.time'));
    }

    /**
     * Determine weather type based on temperature.
     *
     * @return string
     */
    public function parseType(): string
    {
        return $this->parseTemp() >= 0
            ? 'rain'
            : 'snow';
    }
}