<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = User::query();
    }

    public function getUsersForWeatherNotifications(): Collection
    {
        return $this->query->with('cities')
            ->withWhereHas('settings', function ($query) {
                $query->whereRaw("(settings->'weather'->>'alert_enabled')::BOOLEAN = TRUE");
                $query->whereRaw("(settings->'weather'->>'pause_enabled' IS NULL OR (settings->'weather'->>'pause_enabled')::TIMESTAMP < CURRENT_TIMESTAMP)");
            })
            ->get();
    }
}