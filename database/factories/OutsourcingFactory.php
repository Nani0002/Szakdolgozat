<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Outsourcing>
 */
class OutsourcingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween();
        return [
            "entry_time" => fake()->date(),
            "outsourced_number" => fake()->numberBetween(),
            "outsourced_price" => $price,
            "our_price" => $price * 1.2,
            "finished" => fake()->randomElement(["ongoing", "finished", "brought"]),
        ];
    }
}
