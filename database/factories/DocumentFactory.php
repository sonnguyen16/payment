<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_request_id' => \App\Models\PaymentRequest::factory(),
            'filename' => fake()->uuid() . '.pdf',
            'original_name' => fake()->word() . '.pdf',
            'path' => 'documents/' . fake()->uuid() . '.pdf',
            'type' => fake()->randomElement(['invoice', 'receipt', 'contract', 'other']),
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(10000, 5000000),
            'uploaded_by' => \App\Models\User::factory(),
            'uploaded_at' => now(),
        ];
    }
}
