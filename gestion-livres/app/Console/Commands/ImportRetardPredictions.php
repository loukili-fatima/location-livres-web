<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;

class ImportRetardPredictions extends Command
{
    protected $signature = 'import:retard-predictions';
    protected $description = 'Importe les predictions de risque de retard dans la table rentals';

    public function handle()
    {
        $path = base_path('ml/active_rentals_predictions.csv');

        if (!file_exists($path)) {
            $this->error("Fichier introuvable : {$path}");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $rental = Rental::find($data['id']);

            if ($rental) {
                $rental->risque_retard = $data['risque_retard'];
                $rental->save();
                $count++;
            }
        }

        fclose($file);

        $this->info("Import termine : {$count} locations mises a jour avec leur risque de retard");
    }
}
