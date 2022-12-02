<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tattoo_artist>
 */
class Tattoo_artistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name_company' => $this->faker->company,
            'base_price' => $this->faker->randomFloat(2, 0, 1000),
            'price_per_hour' => $this->faker->randomFloat(2, 0, 1000),
            'instagram' => $this->faker->url,
            'status' => $this->faker->randomElement(['available', 'inactive', 'occupied']),
        ];
    }
}
