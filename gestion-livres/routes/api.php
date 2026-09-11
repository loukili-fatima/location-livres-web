<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategorieFavoriteController;
use App\Http\Controllers\Api\MlPredictionController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\ReportingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/login", [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/user", function (Request $request) {
        return $request->user();
    });

    Route::post("/logout", [AuthController::class, "logout"]);

    Route::get("/books", [BookController::class, "index"]);
    Route::get("/books/{book}", [BookController::class, "show"]);

    Route::get("/rentals", [RentalController::class, "index"]);
    Route::post("/rentals", [RentalController::class, "store"]);
    Route::post("/rentals/{rental}/return", [RentalController::class, "returnBook"]);

    Route::get("/ma-prediction", [MlPredictionController::class, "index"]);
    Route::get("/categorie-favorite", [CategorieFavoriteController::class, "index"]);

    Route::post("/search-by-cover", [App\Http\Controllers\Api\CoverSearchController::class, "search"]);

    Route::middleware("admin")->group(function () {
        Route::get("/reporting", [ReportingController::class, "index"]);
    });
});