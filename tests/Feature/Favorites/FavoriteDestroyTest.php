<?php

namespace Tests\Feature\Favorites;

use App\Models\Contact;
use App\Models\Favorite;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Crypt;

class FavoriteDestroyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_destroy_a_contact_favorite(): void
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
            ->deleteJson('api/v1/favorite/' . $encrypted_id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Contacto favorito Eliminado"
        ]);
        $this->assertDatabaseCount('contacts', 10);
        $this->assertDatabaseEmpty('favorites');
        $this->assertDatabaseMissing('favorites', $favorite->toArray());
    }
    public function test_user_can_no_destroy_a_contact_favorite_not_found(): void
    {
        $user = User::factory()->create();
        $contacts=Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        Favorite::create([
            "user_id" => $user->id,
            "contact_id" => $contacts[0]->id
        ]);
        $encrypted_id = Crypt::encrypt(1000);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->deleteJson('api/v1/favorite/' . $encrypted_id);
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
        $this->assertDatabaseCount('favorites',1);
    }
    public function test_user_can_no_destroy_a_contact_favorite_other_person(): void
    {
        $userRandom = User::factory()->create();
        $user = User::factory()->create();
        $contacts=Contact::factory(10, [
            "user_id" => $userRandom->id
        ])->create();
        $favorite=Favorite::create([
            "user_id" => $userRandom->id,
            "contact_id" => $contacts[0]->id
        ]);
        $encrypted_id = Crypt::encrypt($favorite->id);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->deleteJson('api/v1/favorite/' . $encrypted_id);
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No estás autorizado para elimiinar este contacto favorito"
        ]);
        $this->assertDatabaseCount('contacts', 10);
        $this->assertDatabaseCount('favorites',1);
    }
}
