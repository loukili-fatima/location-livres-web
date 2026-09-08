<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RefreshRetardPredictions extends Command
{
    protected $signature = 'retard:refresh';
    protected $description = 'Rafraichit les predictions de risque de retard pour toutes les locations en cours';

    public function handle()
    {
        $this->info('Etape 1/3 : export des locations actives...');
        $this->call('export:active-rentals-data');

        $this->info('Etape 2/3 : calcul des predictions (Python)...');
        $mlPath = base_path('ml');
        $process = new Process(['python', 'predict_active_rentals.py'], $mlPath);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Erreur lors de l\'execution du script Python :');
            $this->error($process->getErrorOutput());
            return 1;
        }

        $this->line($process->getOutput());

        $this->info('Etape 3/3 : import des predictions en base...');
        $this->call('import:retard-predictions');

        $this->info('Pipeline termine avec succes.');
    }
}
