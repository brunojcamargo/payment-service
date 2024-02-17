<?php

namespace Database\Factories;

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
            'from' => $this->faker->uuid,
            'to' => $this->faker->uuid,
            'value' => $this->faker->randomFloat(2, 0, 1000),
            'type' => $this->faker->randomElement(['deposit', 'transfer', 'payment']),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'canceled']),
        ];
    }
}
