<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportingController extends Controller
{
    public function index(Request $request)
    {
        $livresPopulaires = Rental::select("book_id", DB::raw("count(*) as total"))
            ->groupBy("book_id")
            ->orderByDesc("total")
            ->with("book")
            ->limit(10)
            ->get();

        $totalLivres = Book::count();
        $livresDisponibles = Book::where("disponible", true)->count();
        $tauxDisponibilite = $totalLivres > 0
            ? round(($livresDisponibles / $totalLivres) * 100, 2)
            : 0;

        $locationsParMois = Rental::select(
                DB::raw("DATE_FORMAT(date_emprunt, '%Y-%m') as mois"),
                DB::raw("count(*) as total")
            )
            ->groupBy("mois")
            ->orderBy("mois")
            ->get();

        $rentals = Rental::with("user")->get();
        $penalitesParUtilisateur = $rentals
            ->groupBy("user_id")
            ->map(function ($group) {
                $penaliteTotale = $group->sum(function ($rental) {
                    return $rental->calculerPenalite();
                });

                return [
                    "user_id" => $group->first()->user_id,
                    "nom" => optional($group->first()->user)->name,
                    "penalite_totale" => $penaliteTotale,
                ];
            })
            ->filter(function ($item) {
                return $item["penalite_totale"] > 0;
            })
            ->values();

        return response()->json([
            "livres_populaires" => $livresPopulaires,
            "taux_disponibilite" => $tauxDisponibilite,
            "total_livres" => $totalLivres,
            "livres_disponibles" => $livresDisponibles,
            "locations_par_mois" => $locationsParMois,
            "penalites_par_utilisateur" => $penalitesParUtilisateur,
        ]);
    }
}