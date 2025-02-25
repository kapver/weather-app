<?php

namespace Unit\Weather;

use App\Services\Weather\Sources\OpenWeatherParser;
use App\Services\Weather\Sources\OpenWeatherSource;
use App\Services\Weather\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SourceTest extends TestCase
{
    use RefreshDatabase;

    private array $expectedKeys = [
        'time',
        'temp',
        'uvi',
        'pop',
        'type',
    ];

    private array $cityArgs = [
        'latitude'  => 18.483402,
        'longitude' => -69.929611,
        'city'      => 'Santo Domingo',
    ];

    public function test_weather_parsers_can_process_response(): void
    {
        foreach (WeatherService::getSources() as $source) {
            $sourceName = Str::snake(class_basename($source));
            $mockResponse = $this->getMockResponse($sourceName);
            $mockData = new OpenWeatherParser()->parse($mockResponse);
            $this->assertSource($mockData);
        }
    }

    private function getMockResponse(string $source_name): string
    {
        $mockFilepath = base_path("tests/Mocks/{$source_name}_response.json");

        if (!file_exists($mockFilepath)) {
            $mockResponse = new OpenWeatherSource(...$this->cityArgs)->fetchData();
            file_put_contents($mockFilepath, $mockResponse);
        }

        return file_get_contents($mockFilepath);
    }

    private function assertSource(array $data): void
    {
        foreach ($this->expectedKeys as $key) {
            $this->assertArrayHasKey($key, $data);
            $this->assertNotNull($data[$key]);
        }
    }
}
