<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\MlPrediction;
use Illuminate\Console\Command;

class ImportMlPredictions extends Command
{
    protected $signature = "import:ml-predictions";
    protected $description = "Importe les predictions ML depuis ml/predictions.csv vers la table ml_predictions";

    public function handle()
    {
        $path = base_path("ml/predictions.csv");

        if (!file_exists($path)) {
            $this->error("Fichier introuvable : " . $path);
            return 1;
        }

        $handle = fopen($path, "r");
        $header = fgetcsv($handle);

        MlPrediction::truncate();

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $userId = (int) $data["user_id"];
            $categorieNomOuId = $data["categorie_predite"];
            $confiance = (float) $data["confiance"];

            $category = is_numeric($categorieNomOuId)
                ? Category::find((int) $categorieNomOuId)
                : Category::where("nom", $categorieNomOuId)->first();

            if (!$category) {
                $this->warn("Categorie introuvable pour la ligne user_id={$userId} (valeur: {$categorieNomOuId}), ligne ignoree.");
                continue;
            }

            MlPrediction::create([
                "user_id" => $userId,
                "category_id" => $category->id,
                "confiance" => $confiance,
            ]);

            $count++;
        }

        fclose($handle);

        $this->info("Import termine : {$count} predictions enregistrees.");
        return 0;
    }
}