<?php

declare(strict_types=1);

namespace App\Services\Weather;

use App\Services\Settings;

class ProfileSettings extends Settings
{
    protected string $key = 'profile';

    public function getDefaults(): array
    {
        return [];
    }
}