<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;

abstract class Settings
{
    /**
     * The key used to identify the settings group.
     *
     * @var string
     */
    protected string $key;

    /**
     * Constructor to initialize the Settings instance.
     *
     * @param Authenticatable|null $user The user for whom the settings are being managed.
     */
    public function __construct(protected readonly ?Authenticatable $user = null)
    {
    }

    /**
     * Retrieve the settings array by merging default and saved values.
     *
     * @return array<string, mixed> The merged settings array.
     */
    public function getSettings(): array
    {
        $saved = $this->user->settingsArray[$this->key] ?? [];

        return array_merge($this->getDefaults(), $saved);
    }

    /**
     * Retrieve a specific setting value by its key.
     *
     * @param string $key The key of the setting to retrieve.
     * @return mixed The value of the setting or null if not found.
     */
    public function getSetting(string $key): mixed
    {
        return $this->getSettings()[$key] ?? null;
    }

    /**
     * Save or update the settings for the user.
     *
     * @param array<string, mixed> $updated The updated settings to apply.
     * @return void
     */
    public function saveSettings(array $updated): void
    {
        $settings = $this->user->settings()->firstOrNew();

        $settings->settings = array_merge($settings->settings ?? [], [
            $this->key => array_merge($settings->settings[$this->key] ?? [], $updated)
        ]);

        $this->user->settings()->save($settings);
    }

    /**
     * Retrieve the default settings.
     *
     * @return array<string, mixed> The default settings array.
     */
    abstract public function getDefaults(): array;
}