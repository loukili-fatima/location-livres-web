<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;

class ExportCategoryData extends Command
{
    protected $signature = 'export:category-data';
    protected $description = 'Exporte les donnees user_id/category_id de toutes les locations pour analyse ML';

    public function handle()
    {
        $rentals = Rental::with('book')->get();

        $path = base_path('ml/category_data.csv');
        $file = fopen($path, 'w');

        fputcsv($file, ['user_id', 'category_id']);

        foreach ($rentals as $rental) {
            if ($rental->book) {
                fputcsv($file, [
                    $rental->user_id,
                    $rental->book->category_id,
                ]);
            }
        }

        fclose($file);

        $this->info("Export termine : {$rentals->count()} locations exportees dans ml/category_data.csv");
    }
}