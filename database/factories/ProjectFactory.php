<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('PRJ-####-???')),
            'name' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'budget' => fake()->numberBetween(10000000, 500000000),
            'spent' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
        ];
    }
}
