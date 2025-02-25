<?php

namespace Feature;

use App\Services\Weather\WeatherSettings;
use Livewire\Livewire;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\City;
use App\Livewire\Settings\UpdateSettingsForm;

use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user/settings');
        $response->assertStatus(200);
    }

    public function test_settings_can_be_updated(): void
    {
        $pause_hours = 5;
        $paused_until = now()->addHours($pause_hours)->toDateTimeString();

        $expected = [
            'alert_enabled' => true,
            'average_enabled' => true,
            'pause_enabled' => $pause_hours,
            'pop_threshold' => 0.5,
            'uvi_threshold' => 4,
            'email_enabled' => true,
            'telegram_enabled' => true,
            'telegram_verification_code' => Str::ulid()->toString(),
            'telegram_chat_id' => 1234567890,
        ];

        $this->actingAs($user = User::factory()->create());
        $cities = City::factory()->count(2)->create();

        Livewire::test(UpdateSettingsForm::class)
            ->set('state', $expected)
            ->set('cities', $cities->pluck('id')->toArray())
            ->call('updateWeatherSettings');

        $expected['pause_enabled'] = $paused_until;
        $actual = $user->fresh()->settingsArray['weather'];

        ksort($expected);
        ksort($actual);

        $this->assertEqualsCanonicalizing(
            array_keys(new WeatherSettings($user)->getDefaults()),
            array_keys($expected),
        );

        $this->assertArrayIsIdenticalToArrayIgnoringListOfKeys(
            $expected,
            $actual,
            [],
        );
    }
}
