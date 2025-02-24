<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class WeatherSource implements WeatherSourceInterface
{
    protected mixed $responseData;

    public function __construct(
        protected float $latitude,
        protected float $longitude,
        protected ?string $city = null,
    ) {
    }

    public function getData(array $coordinates = [], ?string $city = null): array
    {
        $this->latitude = $coordinates[0] ?? $this->latitude;
        $this->longitude = $coordinates[1] ?? $this->longitude;
        $this->city = $city ?? $this->city;

        $this->fetchData();
        return $this->parseData();
    }

    protected function fetchData(): void
    {
        $ttl = 1800;
        $url = $this->getUrl();
        $key = $this->getName() . '_' . crc32($url);

        $body = '';

        if (Cache::has($key)) {
            $body = Cache::get($key);
        } else {
            $response = Http::get($url);
            if ($response->status() === 200) {
                $body = $response->getBody()->getContents();
                Cache::put($key, $body, $ttl);
            }
        }

        $this->responseData = json_decode($body, false);
    }

    protected function parseData(): array
    {
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
        return data_get($this->responseData, $key, $default);
    }

    public static function getUviText(mixed $uvi): string
    {
        /**
         * TODO Need to clarify API documentation about values
         */
        return match (true) {
            $uvi < 2 => 'Low (minimal risk)',
            $uvi < 6 => 'Moderate (use protection)',
            $uvi < 8 => 'High (shade, sunscreen)',
            $uvi < 11 => 'Very High (extra protection needed)',
            default => 'Extreme (avoid sun exposure)',
        };
    }

    public static function getPopText(mixed $pop): string
    {
        return match (true) {
            $pop <= 0.2 => 'Low probability (less than 20%)',
            $pop <= 0.5 => 'Moderate probability (20% - 50%)',
            $pop <= 0.8 => 'High probability (50% - 80%)',
            default => 'Very high probability (above 80%)',
        };
    }

    public function getName(): string
    {
        return Str::snake(class_basename($this));
    }

    abstract protected function getUrl(): string;

    abstract protected function parsePop(): float;

    abstract protected function parseUvi(): float;

    abstract protected function parseTemp(): float;

    abstract protected function parseTime(): int;

    abstract protected function parseType(): string;
}