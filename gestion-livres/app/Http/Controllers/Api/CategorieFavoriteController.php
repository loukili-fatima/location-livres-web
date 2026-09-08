<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\CategorySimilarity;
use App\Models\MlPrediction;
use Illuminate\Http\Request;

class CategorieFavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $prediction = MlPrediction::where('user_id', $user->id)
            ->with('category')
            ->latest()
            ->first();

        if (!$prediction) {
            return response()->json([
                'message' => 'Aucune prediction disponible pour le moment.',
            ], 404);
        }

        $categorieFavoriteId = $prediction->category_id;

        $livresDisponibles = Book::with(['author', 'category'])
            ->where('category_id', $categorieFavoriteId)
            ->where('disponible', true)
            ->get();

        if ($livresDisponibles->isNotEmpty()) {
            return response()->json([
                'categorie' => $prediction->category->nom,
                'epuisee' => false,
                'livres' => $livresDisponibles,
            ]);
        }

        $similarite = CategorySimilarity::where('category_id', $categorieFavoriteId)
            ->with('categorieProche')
            ->orderByDesc('score')
            ->first();

        if (!$similarite) {
            return response()->json([
                'categorie' => $prediction->category->nom,
                'epuisee' => true,
                'categorie_proche' => null,
                'livres' => [],
            ]);
        }

        $livresCategorieProche = Book::with(['author', 'category'])
            ->where('category_id', $similarite->categorie_proche_id)
            ->where('disponible', true)
            ->get();

        return response()->json([
            'categorie' => $prediction->category->nom,
            'epuisee' => true,
            'categorie_proche' => $similarite->categorieProche->nom,
            'score_similarite' => round($similarite->score, 2),
            'livres' => $livresCategorieProche,
        ]);
    }
}