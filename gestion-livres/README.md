# Application de gestion de location de livres

Application web développée avec Laravel permettant à des utilisateurs de consulter un catalogue de livres, d'en louer, de suivre leurs locations, et à un administrateur de gérer le catalogue (ajout, modification, suppression).

## Fonctionnalités

- Inscription / connexion (Laravel Breeze)
- Rôles utilisateur / administrateur
- Catalogue de livres avec auteurs et catégories
- Location d'un livre avec règles de gestion :
  - le livre doit être disponible
  - impossible de louer 2 fois le même livre en cours
  - limite de 3 locations actives par utilisateur
- Retour de livre avec calcul automatique de pénalité de retard
- Espace administrateur protégé (ajout / modification / suppression de livres)

## Technologies utilisées

- PHP / Laravel (backend, logique métier)
- MySQL (base de données)
- Blade (templates)
- Tailwind CSS (mise en forme)
- JavaScript (interactions simples côté navigateur)

## Structure de la base de données

- `users` : comptes utilisateurs (avec un champ `role`)
- `authors` : auteurs des livres
- `categories` : catégories de livres
- `books` : catalogue de livres (liés à un auteur et une catégorie)
- `rentals` : locations (liant un livre à un utilisateur, avec dates d'emprunt et de retour)

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

L'application est ensuite accessible sur `http://127.0.0.1:8000`.

## Comptes de test

- Administrateur : `admin@test.com` / `password`
- Utilisateur standard : `testeur@test.com` / `password`
