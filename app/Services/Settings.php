<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;

abstract class Settings
{
    protected string $key;

    public function getSettings(User $user): array
    {
        $saved = $user->settingsArray[$this->key] ?? [];
        return array_merge($this->getDefaults(), $saved);
    }

    public function saveSettings(User $user, array $updated): void
    {
        $settings = $user->settings ?? new UserSetting();
        $settings->settings = array_merge($settings->settings ?? [], [
            $this->key => array_merge($settings->settings[$this->key] ?? [], $updated)
        ]);

        $user->settings()->save($settings);
    }

    abstract public function getDefaults(): array;
}