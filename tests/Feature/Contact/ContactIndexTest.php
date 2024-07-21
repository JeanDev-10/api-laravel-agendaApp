<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContactIndexTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_get_his_contacts(): void
    {
        $userRadmon = User::factory()->create();
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        Contact::factory(20, [
            "user_id" => $userRadmon
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(10, 'data.data');
        $this->assertDatabaseCount('contacts', 30);
    }
    public function test_user_can_get_his_contacts_filter_by_name(): void
    {
        $name = "jean";
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name" => "jean",
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?name=' . $name);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(1, 'data.data');
        $this->assertDatabaseCount('contacts', 11);
    }
    public function test_user_can_get_his_contacts_filter_by_nickname(): void
    {
        $nickname = "jp";
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "nickname" => $nickname,
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?nickname=' . $nickname);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(1, 'data.data');
        $this->assertDatabaseCount('contacts', 11);
    }

    public function test_user_can_get_his_contacts_filter_by_name_not_found(): void
    {
        $name = "pierre";
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name" => "jean",
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?name=' . $name);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(0, 'data.data');
        $this->assertDatabaseCount('contacts', 11);
    }
    public function test_user_can_get_his_contacts_filter_by_phone_not_found(): void
    {
        $phone = "0963150796";
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();

        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?phone=' . $phone);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(0, 'data.data');
        $this->assertDatabaseCount('contacts', 10);
    }
    public function test_user_can_get_his_contacts_filter_by_nickname_not_found(): void
    {
        $nickname = "jp";
        $user = User::factory()->create();
        Contact::factory(10, [
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?nickname=' . $nickname);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(0, 'data.data');
        $this->assertDatabaseCount('contacts', 10);
    }
    public function test_user_can_get_his_contacts_filter_by_name_some_results(): void
    {
        $name = "ean";
        $user = User::factory()->create();
        Contact::factory([
            "name" => "julian",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name" => "jean",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name" => "pierre menean",
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?name=' . $name);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(2, 'data.data');
        $this->assertDatabaseCount('contacts', 3);
    }
    public function test_user_can_get_his_contacts_filter_by_phone_some_results(): void
    {
        $phone = "98";
        $user = User::factory()->create();
        Contact::factory([
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "phone" => "0983150796",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "phone" => "012986321",
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?phone=' . $phone);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(2, 'data.data');
        $this->assertDatabaseCount('contacts', 3);
    }
    public function test_user_can_get_his_contacts_filter_by_nickname_some_results(): void
    {
        $nickname = "jp";
        $user = User::factory()->create();
        Contact::factory([
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "nickname" => "jr",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "nickname" => "jear jp",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "nickname" => "jean jp",
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?nickname=' . $nickname);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(2, 'data.data');
        $this->assertDatabaseCount('contacts', 4);
    }
    public function test_user_can_get_his_contacts_oredr_by_name_and_desc(): void
    {
        $user = User::factory()->create();
        Contact::factory([
            "name"=>"alejandro",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name"=>"celorio",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name"=>"bartolo",
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
         $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?orderBy=name&order=desc');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(3, 'data.data');
        $response->assertJsonPath('data.data.0.name', 'celorio');
        $response->assertJsonPath('data.data.1.name', 'bartolo');
        $response->assertJsonPath('data.data.2.name', 'alejandro');
        $this->assertDatabaseCount('contacts', 3);
    }
    public function test_user_can_get_his_contacts_oredr_by_name_and_asc(): void
    {
        $user = User::factory()->create();
        Contact::factory([
            "name"=>"alejandro",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name"=>"celorio",
            "user_id" => $user->id
        ])->create();
        Contact::factory([
            "name"=>"bartolo",
            "user_id" => $user->id
        ])->create();
        $token = JWTAuth::fromUser($user);
         $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/contact/?orderBy=name&order=asc');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonCount(3, 'data.data');
        $response->assertJsonPath('data.data.0.name', 'alejandro');
        $response->assertJsonPath('data.data.1.name', 'bartolo');
        $response->assertJsonPath('data.data.2.name', 'celorio');
        $this->assertDatabaseCount('contacts', 3);
    }
}
