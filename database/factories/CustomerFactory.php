<?php

namespace Database\Factories;


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
            'full_name' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'email_address' => $this->faker->safeEmail(),
            'address' => $this->faker->address(),
            'created_by' => $this->faker->numberBetween(1,3)
        ];
    }
}
