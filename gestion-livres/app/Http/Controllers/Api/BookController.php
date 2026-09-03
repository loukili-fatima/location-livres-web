<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with(["author", "category"])->get();

        return response()->json($books);
    }

    public function show(Book $book)
    {
        $book->load(["author", "category"]);

        return response()->json($book);
    }
}