<?php

namespace App\Console\Commands;

use App\Models\CategorySimilarity;
use Illuminate\Console\Command;

class ImportCategorySimilarities extends Command
{
    protected $signature = 'import:category-similarities';
    protected $description = 'Importe les similarites entre categories calculees par le script Python (ML)';

    public function handle()
    {
        $path = base_path('ml/category_similarities.csv');

        if (!file_exists($path)) {
            $this->error("Fichier introuvable : ml/category_similarities.csv");
            return;
        }

        CategorySimilarity::truncate();

        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        $compteur = 0;

        while (($ligne = fgetcsv($file)) !== false) {
            CategorySimilarity::create([
                'category_id' => $ligne[0],
                'categorie_proche_id' => $ligne[1],
                'score' => $ligne[2],
            ]);
            $compteur++;
        }

        fclose($file);

        $this->info("Import termine : {$compteur} similarites importees.");
    }
}