<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;

class ExportRetardData extends Command
{
    protected $signature = 'export:retard-data';
    protected $description = 'Exporte les donnees des locations terminees pour entrainer le modele de prediction de retard';

    public function handle()
    {
        $rentals = Rental::with('book')->whereNotNull('date_retour_reelle')->get();

        $path = base_path('ml/retard_data.csv');
        $file = fopen($path, 'w');

        fputcsv($file, [
            'taux_retard_precedent',
            'category_id',
            'jour_semaine_emprunt',
            'mois_emprunt',
            'nb_locations_actives_ce_jour',
            'en_retard',
        ]);

        foreach ($rentals as $rental) {
            $locationsPrecedentes = Rental::where('user_id', $rental->user_id)
                ->whereNotNull('date_retour_reelle')
                ->where('date_emprunt', '<', $rental->date_emprunt)
                ->get();

            $nbPrecedentes = $locationsPrecedentes->count();
            $nbRetardsPrecedents = $locationsPrecedentes->filter(function ($r) {
                return $r->date_retour_reelle > $r->date_retour_prevue;
            })->count();

            $tauxRetardPrecedent = $nbPrecedentes > 0
                ? round($nbRetardsPrecedents / $nbPrecedentes, 2)
                : 0;

            $categoryId = $rental->book->category_id ?? 0;

            $dateEmprunt = \Carbon\Carbon::parse($rental->date_emprunt);
            $jourSemaine = $dateEmprunt->dayOfWeek;
            $mois = $dateEmprunt->month;

            $nbLocationsActives = Rental::where('user_id', $rental->user_id)
                ->where('date_emprunt', '<=', $rental->date_emprunt)
                ->where(function ($q) use ($rental) {
                    $q->whereNull('date_retour_reelle')
                        ->orWhere('date_retour_reelle', '>', $rental->date_emprunt);
                })
                ->count();

            $enRetard = $rental->date_retour_reelle > $rental->date_retour_prevue ? 1 : 0;

            fputcsv($file, [
                $tauxRetardPrecedent,
                $categoryId,
                $jourSemaine,
                $mois,
                $nbLocationsActives,
                $enRetard,
            ]);
        }

        fclose($file);

        $this->info("Export termine : {$rentals->count()} locations exportees dans ml/retard_data.csv");
    }
}
