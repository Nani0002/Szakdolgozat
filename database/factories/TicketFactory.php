<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(["open", "started", "ongoing", "price_offered", "waiting", "to_invoice", "closed"]);
        return [
            "title" => fake()->text(50),
            "text" => fake()->text(200),
            "status" => $status,
            "slot_number" => 0,
        ];
    }
}
