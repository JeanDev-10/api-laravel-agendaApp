<?php

namespace Tests\Feature\Favorites;

use Crypt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoriteShowTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_see_a_contact_favorite(): void
    {
        $user = User::factory()->create();
        $contacts = Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        $favorite = Favorite::create([
            "user_id" => $user->id,
            "contact_id" => $contacts[0]->id
        ]);
        $encrypted_id = Crypt::encrypt($favorite->id);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/favorite/' . $encrypted_id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Contacto favorito"
        ]);
        $response->assertJsonPath('data.contact.name', $contacts[0]->name);
        $response->assertJsonCount(2, 'data');
        $this->assertDatabaseCount('contacts', 10);
        $this->assertDatabaseCount('favorites', 1);
        $this->assertDatabaseHas('favorites', $favorite->toArray());
    }
    public function test_user_can_no_see_a_contact_favorite_not_found(): void
    {
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        $encrypted_id = Crypt::encrypt(1000);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/favorite/' . $encrypted_id);
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No se ha encontrado el contacto favorito"
        ]);
        $this->assertDatabaseCount('contacts', 10);
        $this->assertDatabaseCount('favorites', 0);
    }
    public function test_user_can_no_see_a_contact_favorite_another_person(): void
    {
        $user = User::factory()->create();
        $userRamdon = User::factory()->create();
        $contacts=Contact::factory(10, [
            "user_id" => $userRamdon->id
        ])->create();
        $favorite=Favorite::create([
            "user_id" => $userRamdon->id,
            "contact_id" => $contacts[0]->id
        ]);
        $encrypted_id = Crypt::encrypt($favorite->id);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/favorite/' . $encrypted_id);
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No estás autorizado para ver este contacto favorito"
        ]);
        $this->assertDatabaseCount('contacts', 10);
        $this->assertDatabaseCount('favorites', 1);
    }
}
