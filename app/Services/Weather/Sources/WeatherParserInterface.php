<?php

namespace App\Services\Weather\Sources;

/**
 * Interface WeatherParserInterface
 * Defines methods for parsing weather data.
 */
interface WeatherParserInterface
{
    /**
     * Parse the probability of precipitation.
     *
     * @return float Probability of precipitation as a percentage.
     */
    public function parsePop(): float;

    /**
     * Parse the UV index value.
     *
     * @return float UV index value.
     */
    public function parseUvi(): float;

    /**
     * Parse the temperature.
     *
     * @return float Temperature value (in degrees).
     */
    public function parseTemp(): float;

    /**
     * Parse the time.
     *
     * @return int Time represented as a UNIX timestamp.
     */
    public function parseTime(): int;

    /**
     * Parse the weather type description.
     *
     * @return string Weather type as a descriptive string.
     */
    public function parseType(): string;
}