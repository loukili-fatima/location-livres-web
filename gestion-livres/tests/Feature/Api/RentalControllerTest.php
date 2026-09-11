<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_rent_an_available_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(["disponible" => true]);

        $response = $this->actingAs($user, "sanctum")
            ->postJson("/api/rentals", ["book_id" => $book->id]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("rentals", [
            "book_id" => $book->id,
            "user_id" => $user->id,
        ]);
        $this->assertFalse($book->fresh()->disponible);
    }

    public function test_user_cannot_rent_an_unavailable_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(["disponible" => false]);

        $response = $this->actingAs($user, "sanctum")
            ->postJson("/api/rentals", ["book_id" => $book->id]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing("rentals", ["book_id" => $book->id]);
    }

    public function test_user_cannot_rent_same_book_twice_while_active(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(["disponible" => true]);

        Rental::factory()->create([
            "user_id" => $user->id,
            "book_id" => $book->id,
            "date_retour_reelle" => null,
        ]);

        $response = $this->actingAs($user, "sanctum")
            ->postJson("/api/rentals", ["book_id" => $book->id]);

        $response->assertStatus(422);
        $response->assertJsonFragment(["message" => "Vous avez deja une location en cours pour ce livre."]);
    }

    public function test_user_cannot_exceed_three_active_rentals(): void
    {
        $user = User::factory()->create();

        Rental::factory()->count(3)->create([
            "user_id" => $user->id,
            "date_retour_reelle" => null,
        ]);

        $newBook = Book::factory()->create(["disponible" => true]);

        $response = $this->actingAs($user, "sanctum")
            ->postJson("/api/rentals", ["book_id" => $newBook->id]);

        $response->assertStatus(422);
        $response->assertJsonFragment(["message" => "Vous avez atteint la limite de 3 locations actives."]);
    }

    public function test_user_can_return_their_own_rental(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(["disponible" => false]);
        $rental = Rental::factory()->create([
            "user_id" => $user->id,
            "book_id" => $book->id,
            "date_retour_reelle" => null,
        ]);

        $response = $this->actingAs($user, "sanctum")
            ->postJson("/api/rentals/{$rental->id}/return");

        $response->assertStatus(200);
        $this->assertNotNull($rental->fresh()->date_retour_reelle);
        $this->assertTrue($book->fresh()->disponible);
    }

    public function test_user_cannot_return_another_users_rental(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $rental = Rental::factory()->create([
            "user_id" => $owner->id,
            "date_retour_reelle" => null,
        ]);

        $response = $this->actingAs($intruder, "sanctum")
            ->postJson("/api/rentals/{$rental->id}/return");

        $response->assertStatus(403);
    }
}