<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_see_his_information(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->getJson('api/v1/auth/profile');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJsonPath('data.firstname', $user->firstname);
        $response->assertJsonPath('data.id', $user->id);
    }
    public function test_user_can_no_logged_to_see_his_information(): void
    {
        $response = $this
            ->getJson('api/v1/auth/profile');
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message"=>"No autenticado",
            "error"=>true,
            "statusCode"=>401
        ]);
        $this->assertGuest();

    }
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/logout');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
        ]);
        $response->assertJson([
            "message" => "Cierre de sesión exitoso",
            "statusCode" => 200,
            "error" => false
        ]);
        $this->assertGuest();
    }
    public function test_user_can_no_logged_to_logout(): void
    {
        $response = $this
            ->postJson('api/v1/auth/logout');
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message"=>"No autenticado",
            "error"=>true,
            "statusCode"=>401
        ]);
        $this->assertGuest();
    }
}
