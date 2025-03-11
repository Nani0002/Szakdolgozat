<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'post_code' => fake()->postcode(),
            'city' => fake()->city(),
            'street' => fake()->streetAddress(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'type' => fake()->randomElement(["customer", "partner"])
        ];
    }
}
