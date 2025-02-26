<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_users_returns_users_with_alerts_enabled(): void
    {
        $userWithAlerts = User::factory()->create();
        $userWithoutAlerts = User::factory()->create();

        $userWithAlerts->settings()->create(['settings' => ['weather' => ['alert_enabled' => true]]]);
        $userWithoutAlerts->settings()->create(['settings' => ['weather' => ['alert_enabled' => false]]]);

        $repository = new UserRepository();
        $users = $repository->getUsersForWeatherNotifications();

        $this->assertTrue($users->contains($userWithAlerts));
        $this->assertFalse($users->contains($userWithoutAlerts));
    }
}