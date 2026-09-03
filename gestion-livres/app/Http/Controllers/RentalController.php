<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = auth()->user()->rentals()->with('book')->latest()->get();
        return view('rentals.index', compact('rentals'));
    }

    public function store(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);

        $book = Book::findOrFail($request->book_id);
        $user = auth()->user();

        // Condition 1 : le livre doit être disponible
        if (!$book->disponible) {
            return redirect()->back()->with('error', 'Ce livre n\'est plus disponible.');
        }

        // Condition 2 : l'utilisateur ne peut pas louer 2 fois le même livre en cours
        $dejaEnCours = Rental::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('date_retour_reelle')
            ->exists();

        if ($dejaEnCours) {
            return redirect()->back()->with('error', 'Vous avez déjà ce livre en cours de location.');
        }

        // Condition 3 : limite de 3 locations actives maximum
        $nombreActives = Rental::where('user_id', $user->id)
            ->whereNull('date_retour_reelle')
            ->count();

        if ($nombreActives >= 3) {
            return redirect()->back()->with('error', 'Vous avez atteint la limite de 3 locations actives.');
        }

        Rental::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'date_emprunt' => now(),
            'date_retour_prevue' => now()->addDays(14),
        ]);

        $book->update(['disponible' => false]);

        return redirect()->route('books.index')->with('success', 'Livre loué avec succès !');
    }

    public function returnBook(Rental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        $penalite = $rental->calculerPenalite();

        $rental->update(['date_retour_reelle' => now()]);
        $rental->book->update(['disponible' => true]);

        $message = $penalite > 0
            ? "Livre retourné. Pénalité de retard : {$penalite} €."
            : 'Livre retourné dans les délais.';

        return redirect()->route('rentals.index')->with('success', $message);
    }
}
