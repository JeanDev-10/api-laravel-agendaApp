<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserChangePasswordTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_user_change_password(): void
    {
        $password="12345";
        $user = User::factory()->create([
            "password" => Hash::make($password)
        ]);
        $token = JWTAuth::fromUser($user);
        $data=[
            'password' => $password,
            'new_password' => "cambio123",
            'new_password_confirmation' => "cambio123",
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/changePassword',$data);
        $response->assertStatus(200);
        $response->assertJson([
            "message"=>"Contraseña actualizada exitosamente",
            "statusCode"=>200,
            "error"=>false
        ]);
    }
    public function test_user_can_no_change_password_same_password(): void
    {
        $password="12345";
        $user = User::factory()->create([
            "password" => Hash::make($password)
        ]);
        $token = JWTAuth::fromUser($user);
        $data=[
            'password' => $password,
            'new_password' => "12345",
            'new_password_confirmation' => "12345",
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/changePassword',$data);
        $response->assertStatus(422);
        $response->assertJson([
            "message"=>"No hay cambios en las contraseñas",
            "statusCode"=>422,
            "error"=>true
        ]);
    }
    public function test_user_can_no_change_password_incorrect_current_password(): void
    {
        $password="123456";
        $user = User::factory()->create([
            "password" => Hash::make($password)
        ]);
        $token = JWTAuth::fromUser($user);
        $data=[
            'password' => "incorrecta",
            'new_password' => "12345",
            'new_password_confirmation' => "12345",
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/changePassword',$data);
        $response->assertStatus(401);
        $response->assertJson([
            "message"=>"Contraseña actual incorrecta",
            "statusCode"=>401,
            "error"=>true
        ]);
    }
    public function test_user_can_no_change_password_incorrect_error_validation(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $data=[
            'password' => "incorrecta",
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->postJson('api/v1/auth/changePassword',$data);
        $response->assertStatus(422);
        $response->assertJson([
            "message"=>"Error de validación",
            "statusCode"=>422,
            "error"=>true
        ]);
        $response->assertJsonPath('data.new_password',['El campo new password es obligatorio.']);
    }
}
