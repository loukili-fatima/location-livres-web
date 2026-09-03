<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportMlData extends Command
{
    protected $signature = 'export:ml-data';

    protected $description = 'Exporte les données de location vers un CSV pour le machine learning';

    public function handle(): int
    {
        $rentals = Rental::with(['book.category', 'book.author', 'user'])->get();

        $dossier = base_path('ml');
        if (!File::exists($dossier)) {
            File::makeDirectory($dossier, 0755, true);
        }

        $chemin = $dossier . '/rentals_export.csv';
        $handle = fopen($chemin, 'w');

        fputcsv($handle, [
            'user_id',
            'category_id',
            'author_id',
            'jour_semaine_emprunt',
            'en_retard',
        ]);

        foreach ($rentals as $rental) {
            if (!$rental->book) {
                continue;
            }

            $enRetard = $rental->calculerPenalite() > 0 ? 1 : 0;
            $jourSemaine = \Carbon\Carbon::parse($rental->date_emprunt)->dayOfWeek;

            fputcsv($handle, [
                $rental->user_id,
                $rental->book->category_id,
                $rental->book->author_id,
                $jourSemaine,
                $enRetard,
            ]);
        }

        fclose($handle);

        $this->info("Export terminé : {$chemin} ({$rentals->count()} lignes)");

        return Command::SUCCESS;
    }
}
