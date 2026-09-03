<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\MlPredictionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::post('/rentals/{rental}/return', [RentalController::class, 'returnBook'])->name('rentals.return');
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('/ma-prediction', [MlPredictionController::class, 'index'])->name('ml-predictions.index');

    Route::middleware(['admin'])->group(function () {
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

        Route::get('/reporting', [ReportingController::class, 'index'])->name('reporting.index');
    });
});

require __DIR__.'/auth.php';
