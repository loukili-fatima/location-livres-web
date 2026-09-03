<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Roman', 'Science-fiction', 'Philosophie', 'Classique', 'Poésie'];

        foreach ($categories as $nom) {
            Category::firstOrCreate(['nom' => $nom]);
        }
    }
}
