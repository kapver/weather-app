<?php

namespace Tests\Feature\Weather;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\City;
use App\Models\User;
use App\Notifications\WeatherNotification;
use App\Repositories\UserRepositoryInterface;

use App\Services\Weather\WeatherService;
use App\Services\Weather\WeatherSettings;
use App\Services\Weather\WeatherDataFilter;
use App\Services\Weather\Sources\OpenMeteoSource;
use App\Services\Weather\Sources\OpenWeatherSource;

use Tests\Traits\MockHelperTrait;
use Tests\TestCase;

use Mockery;

class WeatherServiceTest extends TestCase
{
    use MockHelperTrait;
    use RefreshDatabase;

    public function test_get_sources_returns_valid_sources(): void
    {
        $sources = WeatherService::getSources();
        $this->assertIsArray($sources);
        $this->assertContains(OpenWeatherSource::class, $sources);
        $this->assertContains(OpenMeteoSource::class, $sources);
    }

    public function test_get_cities_returns_unique_cities(): void
    {
        $mockRepo = Mockery::mock(UserRepositoryInterface::class);
        $mockRepo->shouldReceive('getUsersForWeatherNotifications')->andReturn(new Collection());

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $city = City::factory()->create();
        $user1->cities()->attach($city);
        $user2->cities()->attach($city);

        $cities = new WeatherService($mockRepo)
            ->getCities(collect([$user1, $user2]));

        $this->assertCount(1, $cities);
        $this->assertEquals($city->id, $cities->first()->id);
    }


    public function test_get_relevant_data_returns_empty_when_no_alerts(): void
    {
        $userSettings = [
            'average_enabled' => true,
            'pop_threshold' => 0.5,
            'uvi_threshold' => 3,
        ];
        $citiesData = collect([
            'TestCity' => [
                'source1' => ['pop' => 0.2, 'uvi' => 2],
                'source2' => ['pop' => 0.3, 'uvi' => 1],
            ],
        ]);

        $result = WeatherDataFilter::getRelevantData($userSettings, $citiesData);

        $this->assertTrue($result->isEmpty());
    }

    public function test_process_calls_repository_and_sends_notifications(): void
    {
        // Create a user with weather settings
        $user = User::factory()->create();

        new WeatherSettings($user)->saveSettings($this->userSettings);

        $city = City::factory()->create([
            'type' => 'primary',
            'name' => $this->cityArgs['city'],
            'coordinates' => [$this->cityArgs['longitude'], $this->cityArgs['latitude']],
        ]);

        $user->cities()->attach($city);

        // Mock the repository
        $mockRepo = Mockery::mock(UserRepositoryInterface::class);
        $mockRepo->shouldReceive('getUsersForWeatherNotifications')
            ->once()
            ->andReturn(new Collection([$user]));

        Notification::fake();

        $service = new WeatherService($mockRepo);
        $service->process();

        Log::info('Queue connection: ' . config('queue.default'));

        Notification::assertSentTo($user, WeatherNotification::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
