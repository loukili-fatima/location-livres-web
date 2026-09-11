<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class CoverSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            "photo" => "required|image|max:10240",
        ]);

        $uploadedFile = $request->file("photo");
        $fullPath = $uploadedFile->getRealPath();

        if (!$fullPath || !file_exists($fullPath)) {
            return response()->json(["error" => "Fichier uploade introuvable"], 400);
        }

        $mlPath = base_path("ml");
        $process = new Process(["python", "find_book_by_cover.py", $fullPath], $mlPath);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json([
                "error" => "Erreur lors de l'analyse de l'image",
                "details" => $process->getErrorOutput(),
            ], 500);
        }

        $output = json_decode($process->getOutput(), true);

        if (isset($output["error"])) {
            return response()->json(["error" => $output["error"]], 400);
        }

        $matches = collect($output["matches"])->map(function ($match) {
            $book = Book::with(["author", "category"])->find($match["book_id"]);

            if (!$book) {
                return null;
            }

            return [
                "book_id" => $book->id,
                "titre" => $book->titre,
                "auteur" => optional($book->author)->name,
                "categorie" => optional($book->category)->name,
                "disponible" => $book->disponible,
                "distance" => $match["distance"],
                "confiance" => max(0, 100 - ($match["distance"] * 3)),
            ];
        })->filter()->values();

        return response()->json(["matches" => $matches]);
    }
}