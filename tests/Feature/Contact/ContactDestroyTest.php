<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContactDestroyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_destroy_a_contact(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory([
            "user_id" => $user->id
        ])->create();
        $this->assertDatabaseHas('contacts', $contact->toArray());
        $token = JWTAuth::fromUser($user);
        $encrypted_id = Crypt::encrypt($contact->id);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])->deleteJson('api/v1/contact/'.$encrypted_id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se borró correctamente el contacto."
        ]);
        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }
    public function test_user_can_no_destroy_contact_not_found(): void
    {

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $encrypted_id = Crypt::encrypt(1);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->deleteJson('api/v1/contact/'.$encrypted_id);
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
    public function test_user_can_no_destroy_a_contact_other_person(): void
    {

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
        ])->deleteJson('api/v1/contact/'.$encrypted_id);
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No estás autorizado para realizar esta acción."
        ]);
        $this->assertDatabaseHas('contacts',$contact->toArray());
    }
}
