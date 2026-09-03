<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportBooksFromOpenLibrary extends Command
{
    protected $signature = 'import:books {--limit=20 : Nombre de livres par sujet}';

    protected $description = 'Importe de vrais livres depuis l\'API publique Open Library';

    protected array $sujets = [
        'fiction' => 'Roman',
        'science_fiction' => 'Science-fiction',
        'philosophy' => 'Philosophie',
        'fantasy' => 'Fantastique',
        'history' => 'Histoire',
        'poetry' => 'Poésie',
        'biography' => 'Biographie',
        'mystery' => 'Policier',
    ];

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $totalImportes = 0;

        foreach ($this->sujets as $sujetApi => $nomCategorie) {
            $this->info("Import du sujet : {$sujetApi} ({$nomCategorie})...");

            $url = "https://openlibrary.org/search.json";
            $response = Http::timeout(15)->withOptions([
    'force_ip_resolve' => 'v4',
])->get($url, [
                'subject' => $sujetApi,
                'limit' => $limit,
                'fields' => 'title,author_name,isbn,first_publish_year',
            ]);

            if (!$response->successful()) {
                $this->error("Échec de la requête pour {$sujetApi}");
                continue;
            }

            $docs = $response->json('docs') ?? [];
            $category = Category::firstOrCreate(['nom' => $nomCategorie]);

            foreach ($docs as $doc) {
                $titre = $doc['title'] ?? null;
                if (!$titre) {
                    continue;
                }

                $nomAuteur = $doc['author_name'][0] ?? 'Auteur inconnu';
                $isbn = $doc['isbn'][0] ?? null;

                $author = Author::firstOrCreate(['nom' => $nomAuteur]);

                try {
                    $existe = Book::where('titre', $titre)
                        ->where('author_id', $author->id)
                        ->exists();

                    if ($existe) {
                        continue;
                    }

                    Book::create([
                        'titre' => $titre,
                        'isbn' => $isbn,
                        'author_id' => $author->id,
                        'category_id' => $category->id,
                        'disponible' => rand(1, 100) <= 70,
                    ]);

                    $totalImportes++;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $this->info("Import terminé : {$totalImportes} livres réels ajoutés.");

        return Command::SUCCESS;
    }
}