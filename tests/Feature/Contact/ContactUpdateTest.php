<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContactUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_update_contact(): void
    {
        $data = [
            "name" => "Jean Pierre",
            "phone" => "0963150796",
            "nickname" => "JPR",
        ];
        $user = User::factory()->create();
        $contact=Contact::factory([
            "user_id"=>$user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $encrypted_id = Crypt::encrypt($contact->id);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->putJson('api/v1/contact/'.$encrypted_id , $data);
        $response->assertStatus(202);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se actualizó correctamente el contacto"
        ]);
        $this->assertDatabaseHas('contacts', $data);
        $this->assertDatabaseMissing('contacts', $contact->toArray());
    }
    public function test_user_can_no_update_contact_not_found(): void
    {
        $data = [
            "name" => "Jean Pierre",
            "phone" => "0963150796",
            "nickname" => "JPR",
        ];
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);
        $encrypted_id = Crypt::encrypt(1);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->putJson('api/v1/contact/'.$encrypted_id , $data);
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Contacto no encontrado"
        ]);
        $this->assertDatabaseCount('contacts',0);
    }
    public function test_user_can_update_contact_error_validations(): void
    {
        $data = [
            "name" => "Jean Pierre",
        ];
        $user = User::factory()->create();
        $contact=Contact::factory([
            "user_id"=>$user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $encrypted_id = Crypt::encrypt($contact->id);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->putJson('api/v1/contact/'.$encrypted_id , $data);
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
        $response->assertJsonPath('data.phone',['El campo teléfono es obligatorio.']);
        $this->assertDatabaseHas('contacts',$contact->toArray());
    }
    public function test_user_can_no_update_phone_duplicate(): void
    {
        $data = [
            "name" => "Jean Pierre",
            "phone" => "0963150796",
            "nickname" => "JPR",
        ];
        $user = User::factory()->create();
        $contact1=Contact::factory([
            "user_id"=>$user->id
        ])->create();
        Contact::factory([
            "phone"=>"0963150796",
            "user_id"=>$user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $encrypted_id = Crypt::encrypt($contact1->id);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->putJson('api/v1/contact/'.$encrypted_id, $data);
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
        $response->assertJsonPath("data.phone",["El campo teléfono ya ha sido registrado."]);
        $this->assertDatabaseHas('contacts', $contact1->toArray());
        $this->test_user_can_update_contact();

    }

    public function test_user_can_no_update_a_contact_other_person(): void
    {
        $data = [
            "name" => "Jean Pierre",
            "phone" => "0963150796",
            "nickname" => "JPR",
        ];
        $user = User::factory()->create();
        $contact = Contact::factory([
            "user_id" => $user->id
        ])->create();
        $user2 = User::factory()->create();
        $this->assertDatabaseHas('contacts', $contact->toArray());
        $token = JWTAuth::fromUser($user2);
        $encrypted_id = Crypt::encrypt($contact->id);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])->putJson('api/v1/contact/'.$encrypted_id,$data);
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No estás autorizado para actualizar este contacto"
        ]);
        $this->assertDatabaseHas('contacts',$contact->toArray());
        $this->assertDatabaseMissing('contacts',$data);
    }
}
