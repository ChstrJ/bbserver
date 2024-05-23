<?php

namespace Database\Factories;

use App\Http\Helpers\product\ProductService;
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
            'product_code' => 'BB'. $this->faker->unique()->numberBetween(1, 100),
            'category_id' => $this->faker->numberBetween(1, 8), 
            'name' => $this->faker->randomElement(['Hazelnut', 'Mocha', 'Matcha', 'Italian', 'Supremo', 'Hazelnut', 'Vanilla', 'Chocolate']),
            'description' => $this->faker->randomElement($description),
            'quantity' => $this->faker->numberBetween(50, 100),
            'srp' => $this->faker->randomFloat(2, 500, 1000),
            'member_price' => $this->faker->randomFloat(2, 1, 500),
            'user_id' => $this->faker->numberBetween(1, 2),
            'created_by' => $this->faker->numberBetween(1, 2)
        ];
    }
}
