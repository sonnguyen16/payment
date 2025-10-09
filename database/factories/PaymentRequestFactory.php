<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentRequest>
 */
class PaymentRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'type' => fake()->randomElement(['advance', 'payment_proposal', 'other_expense']),
            'amount' => fake()->numberBetween(100000, 50000000),
            'description' => fake()->sentence(),
            'reason' => fake()->paragraph(),
            'expected_date' => fake()->dateTimeBetween('now', '+1 month'),
            'priority' => fake()->randomElement(['urgent', 'normal']),
            'status' => 'draft',
            'project_id' => \App\Models\Project::factory(),
        ];
    }
}
