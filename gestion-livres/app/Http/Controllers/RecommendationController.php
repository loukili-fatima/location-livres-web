<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Historique de locations de l'utilisateur, avec le livre associé
        $historique = $user->rentals()->with('book')->get();

        // Livres déjà loués (pour ne pas les recommander à nouveau)
        $livresDejaLoues = $historique->pluck('book_id')->toArray();

        // Catégories préférées (les plus fréquentes dans l'historique)
        $categoriesPreferees = $historique
            ->pluck('book.category_id')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(3);

        // Auteurs préférés (les plus fréquents dans l'historique)
        $auteursPreferes = $historique
            ->pluck('book.author_id')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(3);

        // Recommandations : livres disponibles, dans les catégories/auteurs préférés, jamais loués par l'utilisateur
        $recommandations = Book::with(['author', 'category'])
            ->where('disponible', true)
            ->whereNotIn('id', $livresDejaLoues)
            ->where(function ($query) use ($categoriesPreferees, $auteursPreferes) {
                $query->whereIn('category_id', $categoriesPreferees)
                    ->orWhereIn('author_id', $auteursPreferes);
            })
            ->inRandomOrder()
            ->take(8)
            ->get();

        // Si l'utilisateur n'a pas d'historique, on lui propose simplement des livres disponibles au hasard
        if ($historique->isEmpty()) {
            $recommandations = Book::with(['author', 'category'])
                ->where('disponible', true)
                ->inRandomOrder()
                ->take(8)
                ->get();
        }

        return view('recommendations.index', compact('recommandations'));
    }
}