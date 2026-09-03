<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MlPrediction;
use Illuminate\Http\Request;

class MlPredictionController extends Controller
{
    public function index(Request $request)
    {
        $prediction = MlPrediction::where("user_id", $request->user()->id)
            ->with("category")
            ->latest()
            ->first();

        if (!$prediction) {
            return response()->json([
                "message" => "Aucune prediction disponible pour le moment.",
            ], 404);
        }

        return response()->json([
            "categorie" => $prediction->category->nom,
            "confiance" => $prediction->confiance,
        ]);
    }
}