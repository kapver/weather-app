<?php

namespace App\Services\Weather;

use App\Models\User;
use App\Notifications\WeatherNotification;
use App\Services\Weather\Sources\OpenMeteoSource;
use App\Services\Weather\Sources\OpenWeatherSource;
use App\Services\Weather\Sources\WeatherApiSource;
use App\Services\Weather\Sources\WeatherSource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected array $dataSources = [
        OpenWeatherSource::class,
        // WeatherApiSource::class,
        OpenMeteoSource::class,
    ];

    public function process(): void
    {
        $users = $this->getUsers();
        $cities = $this->getCities($users);
        $info = $this->getInfo($cities);
        $this->sendNotifications($users, $info);
    }

    private function getCities(Collection $users): Collection
    {
        return $users->flatMap(function ($user) {
            return $user->cities->map(function ($city) {
                return $city;
            });
        })->unique('id');
    }

    /**
     * Retrieves users to send weather notifications
     * TODO move to repository
     *
     * @return Collection
     */
    private function getUsers(): Collection
    {
        return User::with('cities')->withWhereHas('settings', function ($query) {
            $query->whereRaw("(settings->'weather'->>'alert_enabled')::BOOLEAN = TRUE");
            $query->whereRaw("(settings->'weather'->>'pause_enabled' IS NULL OR (settings->'weather'->>'pause_enabled')::TIMESTAMP < CURRENT_TIMESTAMP)");
        })->get();
    }

    private function getInfo(Collection $cities): Collection
    {
        return $cities->mapWithKeys(function ($city) {
            $city_data = [];

            foreach ($this->dataSources as $dataSourceClass) {
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
            $user_settings = (new WeatherSettings($user))->getSettings();
            $send_data = $this->getRelevantData($user_settings, $cities_data);

            if (!$send_data->isEmpty()) {
                $user->notify(new WeatherNotification($send_data));
            }
        });
    }

    private function getRelevantData(array $user_settings, Collection $cities_data): Collection
    {
        $func = $user_settings['average_enabled'] // global or per user setting?
            ? 'avg'
            : 'max';

        return $cities_data->map(function ($sources) use($func, $user_settings) {
            $pop  = collect($sources)->pluck('pop')->$func();
            $uvi  = collect($sources)->pluck('uvi')->$func();
            $temp = collect($sources)->pluck('temp')->$func();
            $type = collect($sources)->pluck('type')->first();

            $popAlert = $pop > $user_settings['pop_threshold'];
            $uviAlert = $uvi > $user_settings['uvi_threshold'];

            if (!$popAlert && !$uviAlert) {
                return [];
            }

            $data = [
                'temp' => $temp,
                'type' => $type ?? $temp >= 0 ? 'rain' : 'snow',
            ];

            if ($pop > $user_settings['pop_threshold']) {
                $data['pop'] = $pop;
                $data['pop_text'] = WeatherSource::getPopText($pop);
            }

            if ($uvi > $user_settings['uvi_threshold']) {
                $data['uvi']  = $uvi;
                $data['uvi_text'] = WeatherSource::getUviText($uvi);
            }

            return $data;
        })->filter();
    }
}