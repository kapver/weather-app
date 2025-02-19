<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(1000)->create([
            // 'name' => 'Test User',
            // 'email' => 'test@weather.io',
        ]);

        $users->each(function (User $user) {
            $cities_ids = City::query()
                // ->whereIn('type', ['primary', 'admin'])
                // ->where('country', 'Ukraine')
                ->whereIn('name', ['Dubai', 'Kharkiv'])
                ->inRandomOrder()
                ->limit(2)
                ->pluck('id');

            $user->cities()->sync($cities_ids);

            $user->settings()->create([
                'settings' => [
                    "weather" => [
                        "alert_enabled" => true,
                        "average_enabled" => true,
                        "pause_enabled" => false, // now()->toIso8601String()
                        'pop_threshold' => -1,
                        'uvi_threshold' => -1,
                    ],
                ],
            ]);
        });
    }
}