<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => $this->faker->numberBetween(1, 3),
            'user_id' => $this->faker->numberBetween(1, 2),
            'amount_due' => $this->faker->numberBetween(1, 100),
            'number_of_items' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement(['pending', 'paid']),
            'payment_method' => $this->faker->numberBetween(1, 4),
            'checkouts' => json_encode([
                [
                    'id' => $this->faker->numberBetween(1, 100),
                    'name' => $this->faker->randomElement(['Hazelnut', 'Mocha', 'Matcha', 'Italian', 'Supremo', 'Hazelnut', 'Vanilla', 'Chocolate']),
                    'quantity' => $this->faker->numberBetween(1, 5),
                    'srp' => $this->faker->randomFloat(2, 1, 100),
                ],
                [
                    'id' => $this->faker->numberBetween(1, 100),
                    'name' => $this->faker->randomElement(['Hazelnut', 'Mocha', 'Matcha', 'Italian', 'Supremo', 'Hazelnut', 'Vanilla', 'Chocolate']),
                    'quantity' => $this->faker->numberBetween(1, 5),
                    'srp' => $this->faker->randomFloat(2, 1, 100),
                ],
                [
                    'id' => $this->faker->numberBetween(1, 100),
                    'name' => $this->faker->randomElement(['Hazelnut', 'Mocha', 'Matcha', 'Italian', 'Supremo', 'Hazelnut', 'Vanilla', 'Chocolate']),
                    'quantity' => $this->faker->numberBetween(1, 5),
                    'srp' => $this->faker->randomFloat(2, 1, 100),
                ]
            ])
        ];
    }
}
