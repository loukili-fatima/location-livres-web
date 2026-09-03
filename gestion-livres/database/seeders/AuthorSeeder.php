<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $auteurs = [
            'Antoine de Saint-Exupéry',
            'George Orwell',
            'Albert Camus',
            'Victor Hugo',
            'Gabriel García Márquez',
        ];

        foreach ($auteurs as $nom) {
            Author::firstOrCreate(['nom' => $nom]);
        }
    }
}
