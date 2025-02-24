<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class Settings
{
    protected string $key;

    public function __construct(protected readonly ?Authenticatable $user = null)
    {
    }

    public function getSettings(): array
    {
        $saved = $this->user->settingsArray[$this->key] ?? [];

        return array_merge($this->getDefaults(), $saved);
    }

    public function getSetting(string $key): mixed
    {
        return $this->getSettings()[$key] ?? null;
    }

    public function saveSettings(array $updated): void
    {
        $settings = $this->user->settings ?? new UserSetting();
        $settings->settings = array_merge($settings->settings ?? [], [
            $this->key => array_merge($settings->settings[$this->key] ?? [], $updated)
        ]);

        $this->user->settings()->save($settings);
    }

    abstract public function getDefaults(): array;
}