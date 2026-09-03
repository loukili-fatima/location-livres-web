<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $rentals = Rental::with(["book"])
            ->where("user_id", $request->user()->id)
            ->latest()
            ->get();

        return response()->json($rentals);
    }

    public function store(Request $request)
    {
        $request->validate([
            "book_id" => "required|exists:books,id",
        ]);

        $user = $request->user();
        $book = Book::findOrFail($request->book_id);

        if (!$book->disponible) {
            return response()->json(["message" => "Ce livre n'est pas disponible."], 422);
        }

        $dejaEnCours = Rental::where("user_id", $user->id)
            ->where("book_id", $book->id)
            ->whereNull("date_retour_reelle")
            ->exists();

        if ($dejaEnCours) {
            return response()->json(["message" => "Vous avez deja une location en cours pour ce livre."], 422);
        }

        $nombreActives = Rental::where("user_id", $user->id)
            ->whereNull("date_retour_reelle")
            ->count();

        if ($nombreActives >= 3) {
            return response()->json(["message" => "Vous avez atteint la limite de 3 locations actives."], 422);
        }

        $rental = Rental::create([
            "book_id" => $book->id,
            "user_id" => $user->id,
            "date_emprunt" => now(),
            "date_retour_prevue" => now()->addDays(14),
        ]);

        $book->update(["disponible" => false]);

        return response()->json([
            "message" => "Livre loue avec succes.",
            "rental" => $rental,
        ], 201);
    }

    public function returnBook(Request $request, Rental $rental)
    {
        if ($rental->user_id !== $request->user()->id) {
            return response()->json(["message" => "Non autorise."], 403);
        }

        $penalite = $rental->calculerPenalite();

        $rental->update(["date_retour_reelle" => now()]);
        $rental->book->update(["disponible" => true]);

        $message = $penalite > 0
            ? "Livre retourne. Penalite de retard : {$penalite} euros."
            : "Livre retourne dans les delais.";

        return response()->json([
            "message" => $message,
            "penalite" => $penalite,
        ]);
    }
}