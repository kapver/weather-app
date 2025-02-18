<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@weather.io',
        ]);

        $users->each(function (User $user) {
            $cities_ids = City::whereIn('name', ['Dubai', 'Kharkiv'])
                ->inRandomOrder()
                ->pluck('id');

            $user->cities()->sync($cities_ids);
        });
    }
}