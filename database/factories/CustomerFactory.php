<?php

namespace Database\Factories;

use Database\Seeders\CustomerSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Charles', 'Karl', 'Jim']),
            'phone_number' => $this->faker->phoneNumber()
        ];
    }
}
