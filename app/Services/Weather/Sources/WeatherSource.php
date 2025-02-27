<?php

declare(strict_types=1);

namespace App\Services\Weather\Sources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class WeatherSource implements WeatherSourceInterface
{
    /**
     * @param float $latitude
     * @param float $longitude
     * @param string|null $city
     */
    public function __construct(
        protected float $latitude,
        protected float $longitude,
        protected ?string $city = null,
    ) {
    }

    /**
     * @param array $coordinates
     * @param string|null $city
     * @return array
     */
    public function getData(array $coordinates = [], ?string $city = null): array
    {
        $this->latitude  = $coordinates[0] ?? $this->latitude;
        $this->longitude = $coordinates[1] ?? $this->longitude;
        $this->city      = $city ?? $this->city;

        return $this->resolveParser()->parse($this->fetchData());
    }

    /**
     * Fetches weather data from the API or cache
     *
     * @return mixed
     */
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

    /**
     * Retrieves the name of the weather source
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::snake(class_basename($this));
    }

    /**
     * Generates a unique cache key for the given URL
     *
     * @param string $url
     * @return string
     */
    public function getKey(string $url): string
    {
        return $this->getName() . '_' . crc32($url);

    }

    /**
     * Resolves the appropriate parser for the weather source
     *
     * @return WeatherParser|null
     */
    protected function resolveParser(): ?WeatherParser
    {
        $parserClass = preg_replace('/Source$/', 'Parser', static::class);

        return class_exists($parserClass)
            ? new $parserClass()
            : null;
    }

    /**
     * Get the URL for fetching weather data
     *
     * @return string
     */
    abstract public function getUrl(): string;
}