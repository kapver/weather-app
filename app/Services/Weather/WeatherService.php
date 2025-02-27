<?php

declare(strict_types=1);

namespace App\Services\Weather;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use App\Notifications\WeatherNotification;
use App\Repositories\UserRepositoryInterface;
use App\Services\Weather\Sources\OpenMeteoSource;
use App\Services\Weather\Sources\OpenWeatherSource;

class WeatherService
{
    protected static array $dataSources = [
        OpenWeatherSource::class,
        OpenMeteoSource::class,
    ];

    public function __construct(private readonly UserRepositoryInterface $userRepository) {

    }

    /**
     * Retrieve weather data and send notifications
     */
    public function process(): void
    {
        $users = $this->userRepository->getUsersForWeatherNotifications();
        $cities = $this->getCities($users);
        $info = $this->getInfo($cities);

        $this->sendNotifications($users, $info);
    }


    /**
     * Extracts unique cities for the given users.
     *
     * @param Collection $users Users with associated cities.
     *
     * @return Collection Collection of unique cities.
     */
    public function getCities(Collection $users): Collection
    {
        return $users->flatMap(fn ($user) => $user->cities)->unique('id');
    }
    
    /**
     * Retrieve available weather data sources.
     *
     * @return array List of weather data source class names.
     */
    public static function getSources(): array
    {
        return self::$dataSources;
    }

    
    /**
     * Retrieves weather information for the provided collection of cities.
     *
     * @param Collection $cities The collection of cities to fetch weather data for.
     *
     * @return Collection A collection mapping city names to their weather data from multiple sources.
     */
    public function getInfo(Collection $cities): Collection
    {
        return $cities->mapWithKeys(function ($city) {
            $city_data = [];

            foreach (self::$dataSources as $dataSourceClass) {
                if (!class_exists($dataSourceClass)) {
                    Log::error(__CLASS__ . ' data source class "' . $dataSourceClass . '" not found');
                    continue;
                }

                try {
                    $dataSource = new $dataSourceClass(
                        $city->coordinates->latitude,
                        $city->coordinates->longitude,
                        $city->name,
                    );

                    $city_data[$dataSource->getName()] = $dataSource->getData();
                } catch (\Throwable $e) {
                    Log::error("Error processing {$dataSourceClass}: {$e->getMessage()}");
                }
            }

            if (empty($city_data)) {
                Log::error("All data sources failed fo  city: {$city->name}");
            }

            return [
                $city->name => $city_data,
            ];
        });
    }

    /**
     * Processes each user to filter and send weather data notifications based on user-specific settings
     * and relevant city data.
     *
     * @param Collection $users The collection of users to send notifications to.
     * @param Collection $data The weather data to filter for each user's relevant cities.
     *
     * @return void
     */
    private function sendNotifications(Collection $users, Collection $data): void
    {
        $users->each(function ($user) use ($data) {
            $cities_data = $data->only($user->cities->pluck('name'));
            $user_settings = new WeatherSettings($user)->getSettings();
            $send_data = WeatherDataFilter::getRelevantData($user_settings, $cities_data);

            if (!$send_data->isEmpty()) {
                $user->notify(new WeatherNotification($send_data));
            }
        });
    }
}