<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['Roman', 'Science-fiction', 'Philosophie', 'Classique', 'Poésie', 'Policier', 'Fantastique', 'Biographie', 'Histoire', 'Théâtre'];

        return [
            'nom' => $this->faker->unique()->randomElement($categories),
        ];
    }
}