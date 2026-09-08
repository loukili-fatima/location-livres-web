<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_similarities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('categorie_proche_id')->constrained('categories')->onDelete('cascade');
            $table->float('score');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_similarities');
    }
};
