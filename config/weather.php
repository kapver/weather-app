<?php

return [
    'alert_enabled'   => env('WEATHER_ALERT', false),
    'average_enabled' => env('WEATHER_AVERAGE', false),
    'pause_enabled'   => env('WEATHER_PAUSED_UNTIL'),
    'precipitation'   => env('WEATHER_PRECIPITATION', 10),
    'uvi'             => env('WEATHER_UVI', 5),
];