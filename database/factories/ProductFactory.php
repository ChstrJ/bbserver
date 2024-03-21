<?php

namespace Database\Factories;

use App\Models\Category;
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
        $description = Category::pluck('name')->toArray();
        return [
            'category_id' => $this->faker->numberBetween(1, 4), 
            'name' => $this->faker->randomElement(['Hazelnut', 'Mocha', 'Matcha', 'Italian', 'Supremo', 'Hazelnut', 'Vanilla', 'Chocolate']),
            'description' => $this->faker->randomElement($description),
            'quantity' => $this->faker->numberBetween(50, 100),
            'srp' => $this->faker->randomFloat(2, 1, 1000),
            'member_price' => $this->faker->randomFloat(2, 1, 500),
            'user_id' => $this->faker->numberBetween(1, 2),
        ];
    }
}
