<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(1)->create([
            'name' => 'Test User',
            'email' => 'test@weather.io',
        ]);

        $users->each(function (User $user) {
            $cities_ids = City::query()
                ->whereIn('name', ['Dubai', 'Kharkiv'])
                ->inRandomOrder()
                // ->whereIn('type', ['primary', 'admin'])
                // ->where('country', 'Ukraine')
                // ->limit(2)
                ->pluck('id');

            $user->cities()->sync($cities_ids);

            $user->settings()->create([
                'settings' => [
                    "weather" => [
                        "alert_enabled" => true,
                        "average_enabled" => true,
                        "pause_enabled" => null,
                        'pop_threshold' => -1, // default test value to be sure condition value pass threshold
                        'uvi_threshold' => -1,
                    ],
                ],
            ]);
        });
    }
}