<?php

declare(strict_types=1);

namespace App\Services\Weather;

use App\Models\User;
use App\Notifications\WeatherNotification;
use App\Repositories\UserRepositoryInterface;
use App\Services\Weather\Sources\OpenMeteoSource;
use App\Services\Weather\Sources\OpenWeatherSource;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected static array $dataSources = [
        OpenWeatherSource::class,
        OpenMeteoSource::class,
    ];

    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function process(): void
    {
        $users = $this->userRepository->getUsersForWeatherNotifications();
        $cities = $this->getCities($users);
        $info = $this->getInfo($cities);

        $this->sendNotifications($users, $info);
    }

    public function getCities(Collection $users): Collection
    {
        return $users->flatMap(fn ($user) => $user->cities)->unique('id');
    }

    public static function getSources(): array
    {
        return self::$dataSources;
    }

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
                Log::error("All data sources failed for city: {$city->name}");
            }

            return [
                $city->name => $city_data,
            ];
        });
    }

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