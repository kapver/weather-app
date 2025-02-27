<?php

namespace App\Services\Weather;

use App\Enums\WeatherPopConditionsEnum;
use App\Enums\WeatherUviConditionsEnum;
use Illuminate\Support\Collection;

class WeatherDataFilter
{
    /**
     * Filters and returns relevant weather data for cities based on user settings.
     *
     * @param array $userSettings User-defined settings including thresholds and average_enabled flag.
     * @param Collection $citiesData Weather data for the cities to filter.
     * @return Collection Filtered collection of relevant weather data.
     */
    public static function getRelevantData(array $userSettings, Collection $citiesData): Collection
    {
        $func = $userSettings['average_enabled'] ? 'avg' : 'max';

        return $citiesData->map(function ($sources) use ($func, $userSettings) {
            $pop  = collect($sources)->pluck('pop')->$func();
            $uvi  = collect($sources)->pluck('uvi')->$func();
            $temp = collect($sources)->pluck('temp')->$func();
            $type = collect($sources)->pluck('type')->first();

            $popAlert = $pop > $userSettings['pop_threshold'];
            $uviAlert = $uvi > $userSettings['uvi_threshold'];

            if (!$popAlert && !$uviAlert) {
                return [];
            }

            $data = [
                'temp' => $temp,
                'type' => $type ?? ($temp >= 0 ? 'rain' : 'snow'),
            ];

            if ($popAlert) {
                $data['pop']      = $pop;
                $data['pop_text'] = WeatherPopConditionsEnum::getText($pop);
            }

            if ($uviAlert) {
                $data['uvi']      = $uvi;
                $data['uvi_text'] = WeatherUviConditionsEnum::getText($uvi);
            }

            return $data;
        })->filter();
    }
}