<?php

namespace App\Console\Commands;

use App\Services\TelegramUpdatesService;
use Illuminate\Console\Command;

class TelegramGetUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:get-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates application with telegram flow';

    /**
     * Execute the console command.
     */
    public function handle(TelegramUpdatesService $telegramUpdatesService)
    {
        $telegramUpdatesService->handle();
    }
}
