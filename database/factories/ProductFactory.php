<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => $this->faker->numberBetween(1, 4), 
            'name' => $this->faker->randomElement(['Matcha', 'Chocolate', 'Watermerlon', 'Cheesecake', 'Kapeng Barako']),
            'description' => $this->faker->randomElement(['Decafe', 'Chocolate', 'Capucino', 'Bright', 'Caffeine']),
            'quantity' => $this->faker->numberBetween(50, 100),
            'srp' => $this->faker->randomFloat(2, 1, 1000),
            'member_price' => $this->faker->randomFloat(2, 1, 500),
            'user_id' => $this->faker->numberBetween(1, 2),
        ];
    }
}
