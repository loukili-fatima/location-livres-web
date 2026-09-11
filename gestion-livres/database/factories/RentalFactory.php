<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalFactory extends Factory
{
    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "book_id" => Book::factory(),
            "date_emprunt" => now(),
            "date_retour_prevue" => now()->addDays(14),
            "date_retour_reelle" => null,
        ];
    }
}