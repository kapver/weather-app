<?php

namespace Tests\Feature\Weather;

use App\Services\Weather\WeatherService;

use Illuminate\Support\Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\Traits\MockHelperTrait;
use Tests\TestCase;

class WeatherSourceTest extends TestCase
{
    use RefreshDatabase, MockHelperTrait;

    public function test_weather_sources_fetches_data(): void
    {
        foreach (WeatherService::getSources() as $sourceClass) {
            $response = new $sourceClass(...$this->cityArgs)->fetchData();

            $this->assertNotEmpty($response);
            $this->assetKeysValidation($response, $sourceClass);
        }

    }

    private function assetKeysValidation($response, $sourceClass): void
    {
        $data = json_decode($response, true);

        if (array_key_exists($sourceClass, $this->validationKeys)) {
            foreach ($this->validationKeys[$sourceClass] as $validationKey) {
                if (!Arr::has($data, $validationKey)) {
                    $this->assertTrue(false);
                }
            }
        }
    }
}
