<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => $this->faker->word(),
            'unite_mesure' => $this->faker->randomElement(['g', 'kg', 'ml', 'cl', 'cuillère']),
        ];
    }
}