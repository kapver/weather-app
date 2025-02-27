<?php

namespace Tests\Unit\Weather;

use App\Services\Weather\WeatherService;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use Tests\TestCase;

class SourceTest extends TestCase
{
    use RefreshDatabase;

    private array $expectedKeys = ['time', 'temp', 'uvi', 'pop', 'type'];

    public function test_weather_parser_can_extract(): void
    {
        foreach (WeatherService::getSources() as $sourceClass) {
            $sourceName = Str::snake(class_basename($sourceClass));
            $response = file_get_contents(base_path("tests/Mocks/{$sourceName}_response.json"));

            if($parser = $this->resolveParser($sourceClass)){
                $data = $parser->parse($response);

                foreach ($this->expectedKeys as $key) {
                    $this->assertArrayHasKey($key, $data);
                    $this->assertNotNull($data[$key]);
                }
            }
        }
    }

    public function resolveParser(string $sourceClass)
    {
        $parserClass = preg_replace('/Source$/', 'Parser', $sourceClass);

        return class_exists($parserClass)
            ? new $parserClass()
            : null;
    }
}
