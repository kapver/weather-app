<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum WeatherPauseConditionEnum: int
{
    public static function getOptions(): array
    {
        return collect(range(1, 7))
            ->merge(range(8, 24, 8))
            ->mapWithKeys(function ($number) {
                return [$number => self::formatFunc($number)];
            })->toArray();
    }

    private static function formatFunc(int $number): string
    {
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $numberText = ucfirst($formatter->format($number));
        $hourText = Str::plural('hour', $number);
        return  "{$numberText} {$hourText}";
    }
}
