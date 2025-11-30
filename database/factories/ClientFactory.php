<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->dateTimeBetween('-18 years', '-5 years'), // Mineurs
            'medical_info' => fake()->boolean(30) ? 'Allergie arachides, Asthme léger' : null,
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}