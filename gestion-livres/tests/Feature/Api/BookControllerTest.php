<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_books(): void
    {
        $user = User::factory()->create();
        Book::factory()->count(3)->create();

        $response = $this->actingAs($user, "sanctum")->getJson("/api/books");

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_unauthenticated_user_cannot_list_books(): void
    {
        $response = $this->getJson("/api/books");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_single_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(["titre" => "Un titre de test"]);

        $response = $this->actingAs($user, "sanctum")->getJson("/api/books/{$book->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(["titre" => "Un titre de test"]);
    }

    public function test_viewing_nonexistent_book_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, "sanctum")->getJson("/api/books/99999");

        $response->assertStatus(404);
    }
}