<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Auteurs et catégories
        $authors = Author::factory(20)->create();
        $categories = Category::factory(8)->create();

        // 2. Livres reliés à ces auteurs/catégories
        $books = Book::factory(50)->create();

        // 3. Utilisateurs supplémentaires
        $users = User::factory(30)->create();

        // 4. Locations réparties sur les 6 derniers mois
        for ($i = 0; $i < 150; $i++) {
            $book = $books->random();
            $user = $users->random();

            $dateEmprunt = now()->subDays(rand(1, 180));
            $dateRetourPrevue = (clone $dateEmprunt)->addDays(14);

            // 60% de chances que le livre soit déjà rendu
            $estRendu = rand(1, 100) <= 60;

            $dateRetourReelle = null;
            if ($estRendu) {
                // Rendu entre 5 jours avant et 10 jours après la date prévue (donc parfois en retard)
                $decalage = rand(-5, 10);
                $dateRetourReelle = (clone $dateRetourPrevue)->addDays($decalage);

                // Ne jamais avoir une date de retour dans le futur
                if ($dateRetourReelle->isFuture()) {
                    $dateRetourReelle = now();
                }
            }

            Rental::create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'date_emprunt' => $dateEmprunt,
                'date_retour_prevue' => $dateRetourPrevue,
                'date_retour_reelle' => $dateRetourReelle,
            ]);
        }
    }
}
             