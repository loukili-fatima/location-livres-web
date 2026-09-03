<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $saintExupery = Author::where('nom', 'Antoine de Saint-Exupéry')->first();
        $orwell = Author::where('nom', 'George Orwell')->first();
        $camus = Author::where('nom', 'Albert Camus')->first();
        $hugo = Author::where('nom', 'Victor Hugo')->first();
        $marquez = Author::where('nom', 'Gabriel García Márquez')->first();

        $roman = Category::where('nom', 'Roman')->first();
        $scifi = Category::where('nom', 'Science-fiction')->first();
        $philo = Category::where('nom', 'Philosophie')->first();
        $classique = Category::where('nom', 'Classique')->first();

        $livres = [
            ['titre' => 'Le Petit Prince', 'author_id' => $saintExupery->id, 'category_id' => $roman->id],
            ['titre' => '1984', 'author_id' => $orwell->id, 'category_id' => $scifi->id],
            ['titre' => "L'Étranger", 'author_id' => $camus->id, 'category_id' => $philo->id],
            ['titre' => 'Les Misérables', 'author_id' => $hugo->id, 'category_id' => $classique->id],
            ['titre' => 'Cent ans de solitude', 'author_id' => $marquez->id, 'category_id' => $roman->id],
            ['titre' => 'La Peste', 'author_id' => $camus->id, 'category_id' => $philo->id],
        ];

        foreach ($livres as $livre) {
            Book::firstOrCreate(['titre' => $livre['titre']], $livre);
        }
    }
}