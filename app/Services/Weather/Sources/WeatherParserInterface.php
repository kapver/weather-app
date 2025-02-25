<?php

namespace App\Services\Weather\Sources;

interface WeatherParserInterface
{
    public function parsePop(): float;

    public function parseUvi(): float;

    public function parseTemp(): float;

    public function parseTime(): int;

    public function parseType(): string;
}