<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('weather:alert')->hourly();

// TODO should be a webhook implementation
Schedule::command('telegram:get-updates')->everyTwentySeconds();
