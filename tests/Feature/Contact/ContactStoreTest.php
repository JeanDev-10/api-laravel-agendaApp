<?php

namespace Tests\Feature\Contact;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContactStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_create_contact(): void
    {
        $data = [
            "name" => "Jean Pierre",
            "phone" => "0963150796",
            "nickname" => "JPR",
        ];
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/contact', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se ha creado exitosamente el contacto"
        ]);
        $this->assertDatabaseHas('contacts', $data);
    }
    public function test_user_can_create_contact_error_validation(): void
    {
        $data = [
            "name" => "Jean Pierre",
        ];
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/contact', $data);
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
        $response->assertJsonPath('data.phone', ['El campo teléfono es obligatorio.']);
    }
    public function test_user_can_no_create_contact_same_phone(): void
    {
        $data = [
            "name" => "Jean Pierre",
            "phone" => "0963150796",
            "nickname" => "JPR",
        ];
        $user = User::factory([
            "id"=>1
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/contact', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se ha creado exitosamente el contacto"
        ]);
        $this->assertDatabaseHas('contacts', $data);
        $data2 = [
            "name" => "Jean Rodríguez",
            "phone" => "0963150796"
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])->postJson('api/v1/contact', $data2);
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
        $response->assertJsonPath('data.phone',['El campo teléfono ya ha sido registrado.']);
        $this->assertDatabaseMissing('contacts', $data2);
    }
    public function test_user_can_no_create_contact_same_phone_but_other_user_can(): void
    {
        $data = [
            "name" => "Jean Pierre",
            "phone" => "0963150796",
            "nickname" => "JPR",
        ];
        $user = User::factory([
            "id"=>1
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/contact', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se ha creado exitosamente el contacto"
        ]);
        $this->assertDatabaseHas('contacts', $data);
        $data2 = [
            "name" => "Jean Rodríguez",
            "phone" => "0963150796"
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])->postJson('api/v1/contact', $data2);
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
        $response->assertJsonPath('data.phone',['El campo teléfono ya ha sido registrado.']);
        $this->assertDatabaseMissing('contacts', $data2);
        $this->test_user_can_create_contact();
        $this->assertDatabaseCount('contacts',2);
    }
}
