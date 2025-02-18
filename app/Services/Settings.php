<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

abstract class Settings
{
    protected string $key; // Key for specific settings (e.g., 'weather')

    public function getSettings(User $user): array
    {
        return array_merge($this->getDefaults(), $user->settings[$this->key] ?? []);
    }

    public function saveSettings(User $user, array $settings): void
    {
        $user->settings = array_merge($user->settings ?? [], [
            $this->key => array_merge($this->getSettings($user), $settings),
        ]);
        $user->save();
    }

    abstract public function getDefaults(): array;
}