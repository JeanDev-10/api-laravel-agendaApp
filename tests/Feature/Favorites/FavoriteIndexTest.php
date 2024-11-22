<?php

namespace Tests\Feature\Favorites;

use App\Models\Contact;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoriteIndexTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_get_his_contacts_favorites(): void
    {
        $userRadmon = User::factory()->create();
        $user = User::factory()->create();
        $contacts = Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        for ($i = 0; $i < 5; $i++) {
            Favorite::create([
                'contact_id' => $contacts[$i]->id,
                'user_id' => $user->id,
            ]);
        }
        $contactsRadmon = Contact::factory(20, [
            "user_id" => $userRadmon
        ])->create();
        for ($i = 0; $i < 5; $i++) {
            Favorite::create([
                'contact_id' => $contactsRadmon[$i]->id,
                'user_id' => $userRadmon->id,
            ]);
        }
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/favorite');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Lista de favoritos de un usuario."
        ]);
        $response->assertJsonCount(5, 'data.data');
        $responseData = $response->json('data');
        $decryptedId_c1 = Crypt::decrypt($responseData['data'][0]['contact']['id']);
        $decryptedId_c2 = Crypt::decrypt($responseData['data'][1]['contact']['id']);
        $this->assertEquals($decryptedId_c1, $contacts[0]->id);
        $this->assertEquals($decryptedId_c2, $contacts[1]->id);
        $response->assertJsonPath('data.data.0.contact.name', $contacts[0]->name);
        $response->assertJsonPath('data.data.1.contact.name', $contacts[1]->name);
        $this->assertDatabaseCount('contacts', 30);
        $this->assertDatabaseCount('favorites', 10);
    }
    public function test_user_can_get_his_contacts_favorites_filter_by_name(): void
    {
        $name = "jean";
        $user = User::factory()->create();
        $contacts = Contact::factory(5, [
            "user_id" => $user->id
        ])->create();
        $contact_jean = Contact::factory([
            "name" => "jean",
            "user_id" => $user->id
        ])->create();
        Favorite::create([
            "contact_id" => $contact_jean->id,
            "user_id" => $user->id
        ]);
        for ($i = 0; $i < 2; $i++) {
            Favorite::create([
                'contact_id' => $contacts[$i]->id,
                'user_id' => $user->id,
            ]);
        }
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/favorite?name=' . $name);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Lista de favoritos de un usuario."
        ]);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonPath('data.data.0.contact.name', "jean");
        $this->assertDatabaseCount('contacts', 6);
        $this->assertDatabaseCount('favorites', 3);
    }
    public function test_user_can_get_his_contacts_favorites_filter_by_nickname(): void
    {
        $nickname = "jpz";
        $user = User::factory()->create();
        $contacts = Contact::factory(5, [
            "user_id" => $user->id
        ])->create();
        $contact_jean = Contact::factory([
            "nickname" => "jpz",
            "user_id" => $user->id
        ])->create();
        Favorite::create([
            "contact_id" => $contact_jean->id,
            "user_id" => $user->id
        ]);
        for ($i = 0; $i < 2; $i++) {
            Favorite::create([
                'contact_id' => $contacts[$i]->id,
                'user_id' => $user->id,
            ]);
        }
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/favorite?nickname=' . $nickname);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Lista de favoritos de un usuario."
        ]);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonPath('data.data.0.contact.nickname', "jpz");
        $this->assertDatabaseCount('contacts', 6);
        $this->assertDatabaseCount('favorites', 3);
    }
    public function test_user_can_get_his_contacts_favorites_filter_by_phone(): void
    {
        $phone = "09863123";
        $user = User::factory()->create();
        $contacts = Contact::factory(5, [
            "user_id" => $user->id
        ])->create();
        $contact_jean = Contact::factory([
            "phone" => "09863123",
            "user_id" => $user->id
        ])->create();
        Favorite::create([
            "contact_id" => $contact_jean->id,
            "user_id" => $user->id
        ]);
        for ($i = 0; $i < 2; $i++) {
            Favorite::create([
                'contact_id' => $contacts[$i]->id,
                'user_id' => $user->id,
            ]);
        }
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/favorite?phone=' . $phone);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message" => "Lista de favoritos de un usuario."
        ]);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonPath('data.data.0.contact.phone', $phone);
        $this->assertDatabaseCount('contacts', 6);
        $this->assertDatabaseCount('favorites', 3);
    }

}
