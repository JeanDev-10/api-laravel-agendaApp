<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class ContactShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_get_a_contact(): void
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
        ])->getJson('api/v1/contact/'.$encrypted_id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Mostrando Contacto"
        ]);
        $response->assertJsonPath('data.phone', $contact->phone);
        $response->assertJsonPath('data.name', $contact->name);
        $this->assertDatabaseHas('contacts', $contact->toArray());
    }
    public function test_user_can_no_get_a_contact_other_person(): void
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
        ])->getJson('api/v1/contact/'.$encrypted_id);
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No estás autorizado para ver este contacto"
        ]);
    }

}
