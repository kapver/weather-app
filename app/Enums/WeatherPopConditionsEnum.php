<?php

namespace App\Enums;

enum WeatherPopConditionsEnum: int
{
    case PopAny = -1;
    case PopLow = 20;
    case PopModerate = 50;
    case PopHigh = 80;
    case PopVeryHigh = 100;

    public static function getOptions(): array
    {
        return [
            self::PopAny->value => 'Any probability',
            self::PopLow->value => 'Low probability (less than 20%)',
            self::PopModerate->value => 'Moderate probability (20% - 50%)',
            self::PopHigh->value => 'High probability (50% - 80%)',
            self::PopVeryHigh->value => 'Very high probability (above 80%)',
        ];
    }

    public static function getText(mixed $pop): string
    {
        return match (true) {
            $pop <= 0.2 => 'Low probability (less than 20%)',
            $pop <= 0.5 => 'Moderate probability (20% - 50%)',
            $pop <= 0.8 => 'High probability (50% - 80%)',
            default => 'Very high probability (above 80%)',
        };
    }
}
