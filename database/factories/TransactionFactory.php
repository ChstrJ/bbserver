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
            'amount_due' => $this->faker->numberBetween(1, 100),
            'number_of_items' => $this->faker->numberBetween(1, 100),
            'payment_type' => $this->faker->sentence(),
        ];
    }
}
