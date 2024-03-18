<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->unique()->randomElement(['Chester Jazmin', 'Keanno Regino']),
            'username' => $this->faker->unique()->randomElement(['admin1', 'admin2']),
            'password' => bcrypt('1234')
        ];
    }
}
