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
            'userID' => User::all()->random()->id(),
            'amountDue' => $this->faker->numberBetween(1, 100),
            'numberOfItems' => $this->faker->numberBetween(1, 100),
            'paymentType' => $this->faker->sentence(),
        ];
    }
}
