<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

use JsonException;

abstract class WeatherParser implements WeatherParserInterface
{
    protected mixed $responseObject;

    /**
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

    protected function parseValue($key, $default = null)
    {
        return data_get($this->responseObject, $key, $default);
    }
}