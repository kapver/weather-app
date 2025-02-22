<?php

namespace App\Enums;

enum WeatherPopConditionsEnum: int
{
    case PopLow = 20;
    case PopModerate = 50;
    case PopHigh = 80;
    case PopVeryHigh = 100;

    public static function getOptions(): array
    {
        return [
            self::PopLow->value => 'Low probability (less than 20%)',
            self::PopModerate->value => 'Moderate probability (20% - 50%)',
            self::PopHigh->value => 'High probability (50% - 80%)',
            self::PopVeryHigh->value => 'Very high probability (above 80%)',
        ];
    }
}
