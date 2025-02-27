<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

use JsonException;

abstract class WeatherParser implements WeatherParserInterface
{
    protected mixed $responseObject;

    /**
     * Create the response object from the given input.
     *
     * @param string|array|object|null $response
     * @throws JsonException
     */
    protected function createResponseObject($response): void
    {
        $this->responseObject = is_string($response)
            ? json_decode(
                json: $response,
                associative: false,
                flags: JSON_THROW_ON_ERROR,
            ) : $response;
    }

    /**
     * Parse weather data from the response.
     *
     * @param string|array|object|null $response
     * @return array{
     *     time: int,
     *     temp: float,
     *     uvi: float,
     *     pop: float,
     *     type: string
     * }
     * @throws JsonException
     */
    public function parse($response = null): array
    {
        $this->createResponseObject($response);

        return [
            'time' => $this->parseTime(),
            'temp' => $this->parseTemp(),
            'uvi'  => $this->parseUvi(),
            'pop'  => $this->parsePop(),
            'type' => $this->parseType(),
        ];
    }

    /**
     * Retrieve a value from the response object using a dot-notated key.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    protected function parseValue($key, $default = null)
    {
        return data_get($this->responseObject, $key, $default);
    }
}