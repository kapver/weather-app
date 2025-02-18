<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $latitude = fake()->latitude();
        $longitude = fake()->longitude();

        return [
            'name' => $this->faker->city,
            'country' => $this->faker->country,
            'coordinates' => [$longitude, $latitude],
        ];
    }
}
