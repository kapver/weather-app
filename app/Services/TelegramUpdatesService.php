<?php

namespace App\Services;

use App\Models\User;
use App\Services\Weather\WeatherSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramUpdates;

class TelegramUpdatesService
{
    public function handle(): void
    {
        $lastUpdateId = Cache::get('telegram_last_update_id', 0);

        $response = TelegramUpdates::create()
            ->latest()
            ->options(['offset' => $lastUpdateId + 1])
            ->get();

        if (!$response['ok']) {
            return;
        }

        $json = json_encode($response['result']);

        Cache::put('telegram_updates_' . microtime(), $json);

        foreach ($response['result'] as $update) {
            $lastUpdateId = max($lastUpdateId, $update['update_id']);

            // Check for /start command and extract verification code
            if (isset($update['message']['text']) && str_starts_with($update['message']['text'], '/start ')) {
                [$command, $code] = explode(' ', $update['message']['text']) + [null, null];

                // Verify code and save the chat ID if they match
                if ($user = $this->getUserByTelegramVerificationCode($code)) {
                    $settings = new WeatherSettings($user);
                    if ($telegram_chat_id = $update['message']['chat']['id']) {
                        $settings->saveSettings(['telegram_chat_id' => $telegram_chat_id]);
                    }
                }
            }

            Cache::put('telegram_last_update_id', $lastUpdateId, now()->addMinutes(10));
        }
    }

    /**
     * TODO move to repository
     * @param string $verificationCode
     * @return User|null
     */
    public function getUserByTelegramVerificationCode(string $verificationCode): ?User
    {
        return User::whereHas('settings', function ($query) use ($verificationCode) {
            $query->whereRaw("(settings->'weather'->>'telegram_verification_code') = ?", [$verificationCode]);
        })->first();
    }
}