<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $booksParCategorie = Category::with(['books' => function ($query) {
            $query->with('author')->orderBy('titre');
        }])
        ->orderBy('nom')
        ->get()
        ->filter(fn($category) => $category->books->isNotEmpty());

        return view('books.index', compact('booksParCategorie'));
    }

    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();
        return view('books.create', compact('authors', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Livre ajouté.');
    }

    public function edit(Book $book)
    {
        $authors = Author::all();
        $categories = Category::all();
        return view('books.edit', compact('book', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $book->id,
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Livre modifié.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Livre supprimé.');
    }
}
