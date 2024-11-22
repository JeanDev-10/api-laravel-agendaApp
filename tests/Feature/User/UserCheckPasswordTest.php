<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserCheckPasswordTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_user_check_password(): void
    {
        $password="12345";
        $user = User::factory()->create([
            "password" => Hash::make($password)
        ]);
        $token = JWTAuth::fromUser($user);
        $data=[
            'password' => $password,
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/check-password',$data);
        $response->assertStatus(200);
        $response->assertJson([
            "message"=>"Contraseña correcta!",
            "statusCode"=>200,
            "error"=>false
        ]);
    }
    public function test_can_user_check_password_incorrect(): void
    {
        $password="12345";
        $user = User::factory()->create([
            "password" => Hash::make($password)
        ]);
        $token = JWTAuth::fromUser($user);
        $data=[
            'password' => "pswrong",
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/check-password',$data);
        $response->assertStatus(401);
        $response->assertJson([
            "message"=>"Contraseña actual incorrecta",
            "statusCode"=>401,
            "error"=>true
        ]);
    }
    public function test_can_user_check_password_validation_error(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/check-password',[]);
        $response->assertStatus(422);
        $response->assertJson([
            "message"=>"Error de validación",
            "statusCode"=>422,
            "error"=>true
        ]);
        $response->assertJsonPath('data.password',['El campo contraseña es obligatorio.']);
    }
}
