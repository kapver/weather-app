<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Weather\WeatherService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RunWeatherAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends weather notifications to the users by predefined weather conditions threshold';

    /**
     * Execute the console command.
     */
    public function handle(WeatherService $weatherService): void
    {
        $weatherService->process();
    }
}
