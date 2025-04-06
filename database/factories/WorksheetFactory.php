<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Worksheet>
 */
class WorksheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sheet_number' => fake()->unique()->uuid(),
            'sheet_type' => fake()->randomElement(["maintanance", "paid", "warranty"]),
            'print_date' => fake()->date(),
            'declaration_time' => fake()->date(),
            'declaration_mode' => fake()->randomElement(["email", "phone", "personal", "onsite"]),
            'error_description' => fake()->text(),
            'comment' => fake()->text(25),
            'final' => fake()->boolean(),
            'work_start' => fake()->date(),
            'work_end' => fake()->date(),
            'work_time' => fake()->numberBetween(1,10),
            'work_description' => fake()->text(300),
            'current_step' => fake()->randomElement(["open", "started", "ongoing", "price_offered", "waiting", "to_invoice", "closed"]),
            'slot_number' => 0,
        ];
    }
}
