<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DownloadBookCovers extends Command
{
    protected $signature = 'covers:download';
    protected $description = 'Telecharge les couvertures des livres depuis Open Library via ISBN';

    public function handle()
    {
        $storagePath = storage_path('app/public/covers');

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $books = Book::whereNotNull('isbn')->whereNull('cover_path')->get();
        $count = 0;
        $failed = 0;

        $this->info("Livres a traiter : {$books->count()}");

        foreach ($books as $book) {
            $isbn = trim($book->isbn);
            $url = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";

            $success = false;

            for ($attempt = 1; $attempt <= 5; $attempt++) {
                try {
                    $response = Http::timeout(20)->get($url);

                    if ($response->successful() && strlen($response->body()) > 1000) {
                        $filename = "book_{$book->id}.jpg";
                        file_put_contents("{$storagePath}/{$filename}", $response->body());
                        $book->cover_path = "covers/{$filename}";
                        $book->save();
                        $count++;
                        $success = true;
                        break;
                    }
                } catch (\Exception $e) {
                    if ($attempt < 5) {
                        sleep(2);
                    }
                }
            }

            if (!$success) {
                $failed++;
                $this->warn("Echec definitif pour le livre {$book->id} (ISBN: {$isbn})");
            }

            usleep(300000);
        }

        $this->info("Termine : {$count} couvertures telechargees, {$failed} echecs");
    }
}