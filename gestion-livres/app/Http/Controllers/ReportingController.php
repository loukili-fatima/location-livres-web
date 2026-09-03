<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportingController extends Controller
{
    public function index()
    {
        // Livres les plus loués (top 5)
        $livresPopulaires = Rental::select('book_id', DB::raw('count(*) as nombre_locations'))
            ->with('book')
            ->groupBy('book_id')
            ->orderByDesc('nombre_locations')
            ->take(5)
            ->get();

        // Taux de disponibilité du catalogue
        $totalLivres = Book::count();
        $livresDisponibles = Book::where('disponible', true)->count();
        $tauxDisponibilite = $totalLivres > 0
            ? round(($livresDisponibles / $totalLivres) * 100, 1)
            : 0;

        // Nombre de locations par mois (6 derniers mois)
        $locationsParMois = Rental::select(
                DB::raw("DATE_FORMAT(date_emprunt, '%Y-%m') as mois"),
                DB::raw('count(*) as total')
            )
            ->where('date_emprunt', '>=', now()->subMonths(6))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Utilisateurs avec le plus de pénalités
        $penalitesParUtilisateur = User::with('rentals')->get()->map(function ($user) {
            $totalPenalite = $user->rentals->sum(function ($rental) {
                return $rental->calculerPenalite();
            });
            return [
                'user' => $user,
                'total_penalite' => $totalPenalite,
            ];
        })
        ->filter(fn($item) => $item['total_penalite'] > 0)
        ->sortByDesc('total_penalite')
        ->take(5)
        ->values();

        return view('reporting.index', compact(
            'livresPopulaires',
            'totalLivres',
            'livresDisponibles',
            'tauxDisponibilite',
            'locationsParMois',
            'penalitesParUtilisateur'
        ));
    }
}