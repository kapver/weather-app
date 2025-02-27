<?php

namespace App\Enums;

enum WeatherUviConditionsEnum: int
{
    case UviAny = -1;
    case UviLow = 2;
    case UviModerate = 6;
    case UviHigh = 8;
    case UviVeryHigh = 11;
    case UviUltraHigh = 20;

    public static function getOptions(): array
    {
        return [
            self::UviAny->value => 'Any (any risk)',
            self::UviLow->value => 'Low (minimal risk)',
            self::UviModerate->value => 'Moderate (medium risk)',
            self::UviHigh->value => 'High (high risk)',
            self::UviVeryHigh->value => 'Very high (very high risk)',
            self::UviUltraHigh->value => 'Ultra high (extreme risk)',
        ];
    }

    public static function getText(mixed $uvi): string
    {
        return match (true) {
            $uvi < 2 => 'Low (minimal risk)',
            $uvi < 6 => 'Moderate (use protection)',
            $uvi < 8 => 'High (shade, sunscreen)',
            $uvi < 11 => 'Very High (extra protection needed)',
            default => 'Extreme (avoid sun exposure)',
        };
    }
}
