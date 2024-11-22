<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContactRestoreContactsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_restart_the_contacts_deleted(): void
    {
        $user = User::factory()->create();
        $deletedContacts = Contact::factory(5)->create([
            'user_id' => $user->id,
            'deleted_at' => now(), // Simular soft delete
        ]);
        $activeContacts  = Contact::factory(5,[
            "user_id" => $user->id,
        ])->create();
        foreach ($activeContacts as $contact) {
            $this->assertDatabaseHas('contacts', $contact->toArray());
        }

        foreach ($deletedContacts as $contact) {
            $this->assertDatabaseHas('contacts', $contact->toArray());
            $this->assertSoftDeleted('contacts', $contact->toArray());
        }
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])->postJson('api/v1/contact/restore');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se restauró correctamente los contactos."
        ]);
        $this->assertDatabaseCount('contacts',10);
        foreach ($deletedContacts as $contact) {
            $this->assertDatabaseHas('contacts', $contact->toArray());
            $this->assertNull(Contact::withTrashed()->find($contact->id)->deleted_at);
        }
    }
    public function test_user_can_no_restart_contact_phone_active(): void
    {
        $user = User::factory()->create();
        $deletedContact = Contact::factory([
            'user_id' => $user->id,
            "phone"=>"0963150796",
            'deleted_at' => now()
        ])->create();
        $activeContact  = Contact::factory([
            "user_id" => $user->id,
            "phone"=>"0963150796",
        ])->create();
        $this->assertDatabaseHas('contacts',$activeContact->toArray());
        $this->assertDatabaseHas('contacts', $deletedContact->toArray());
        $this->assertSoftDeleted('contacts', $deletedContact->toArray());
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])->postJson('api/v1/contact/restore');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Se restauró correctamente los contactos."
        ]);
        $this->assertDatabaseCount('contacts',2);
        $this->assertDatabaseHas('contacts',$activeContact->toArray());
        $this->assertDatabaseHas('contacts', $deletedContact->toArray());
        $this->assertSoftDeleted('contacts', $deletedContact->toArray());
    }
    public function test_user_can_no_restart_0_contact_deleted(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        Contact::factory(
            ["user_id"=>$user->id]
        )->create();
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])->postJson('api/v1/contact/restore');
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "No hay contactos para restaurar"
        ]);
        $this->assertDatabaseCount('contacts',1);
    }
}
