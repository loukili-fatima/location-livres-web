<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre' => ucfirst($this->faker->words(3, true)),
            'isbn' => $this->faker->unique()->isbn13(),
            'disponible' => $this->faker->boolean(70),
            'author_id' => Author::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}