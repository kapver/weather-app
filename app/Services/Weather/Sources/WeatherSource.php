<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class WeatherSource implements WeatherSourceInterface
{
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

        return $this->resolveParser()->parse($this->fetchData());
    }

    public function fetchData()
    {
        $res = '';
        $url = $this->getUrl();
        $key = $this->getKey($url);

        if (Cache::has($key)) {
            $res = Cache::get($key);
        } else {
            $response = Http::get($url);
            if ($response->status() === 200) {
                $res = $response->getBody()->getContents();
                Cache::put($key, $res, 1800);
            }
        }

        return $res;
    }

    public function getName(): string
    {
        return Str::snake(class_basename($this));
    }

    public function getKey(string $url): string
    {
        return $this->getName() . '_' . crc32($url);

    }

    protected function resolveParser(): ?WeatherParser
    {
        $parserClass = preg_replace('/Source$/', 'Parser', static::class);

        return class_exists($parserClass)
            ? new $parserClass()
            : null;
    }

    abstract public function getUrl(): string;
}