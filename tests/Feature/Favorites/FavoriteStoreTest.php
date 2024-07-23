<?php

namespace Tests\Feature\Favorites;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoriteStoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_create_a_contact_favorites(): void
    {
        $user = User::factory()->create();
        $contacts = Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        $data = [
            "contact_id" => Crypt::encrypt($contacts[0]->id)
        ];
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/favorite', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se ha creado añadido exitosamente el contacto a favoritos"
        ]);
        $this->assertDatabaseCount('contacts', 10);
        $this->assertDatabaseCount('favorites', 1);
        $this->assertDatabaseHas('favorites', [
            "user_id" => $user->id,
            "contact_id" => $contacts[0]->id
        ]);
    }
    public function test_user_can_no_create_a_contact_favorites_validation_error(): void
    {
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/favorite');
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Error de validación"
        ]);
        $response->assertJsonPath('data.contact_id',['El campo contact id es obligatorio.']);
        $this->assertDatabaseCount('contacts', 10);
        $this->assertDatabaseEmpty("favorites");
    }
    public function test_user_can_no_create_a_contact_favorites_duplication_error(): void
    {
        $user = User::factory()->create();
        $contacts=Contact::factory(3, [
            "user_id" => $user->id
        ])->create();
        Favorite::create([
            "user_id"=>$user->id,
            "contact_id"=>$contacts[0]->id,
        ]);
        $data=[
            "contact_id"=>Crypt::encrypt($contacts[0]->id),
        ];
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/favorite',$data);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Error de validación"
        ]);
        $response->assertJsonPath('data.contact_id',['Este contacto ya está en tu lista de favoritos.']);
        $this->assertDatabaseCount('contacts', 3);
        $this->assertDatabaseCount('favorites', 1);
    }
    public function test_user_can_no_create_a_contact_favorites_not_found_error(): void
    {
        $user = User::factory()->create();
        Contact::factory(3, [
            "user_id" => $user->id
        ])->create();

        $data=[
            "contact_id"=>Crypt::encrypt(1000),
        ];
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/favorite',$data);
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "El recurso solicitado no existe"
        ]);
        $this->assertDatabaseCount('contacts', 3);
        $this->assertDatabaseCount('favorites', 0);
    }
    public function test_user_can_no_create_a_contact_favorites_contact_other_person(): void
    {
        $user = User::factory()->create();
        $userRandom=User::factory()->create();
        $contacts=Contact::factory(3, [
            "user_id" => $userRandom->id
        ])->create();
        $data=[
            "contact_id"=>Crypt::encrypt($contacts[0]->id),
        ];
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/favorite',$data);
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No estás autorizado para añadir este contacto a favoritos"
        ]);
        $this->assertDatabaseCount('contacts', 3);
        $this->assertDatabaseCount('favorites', 0);
    }
}
