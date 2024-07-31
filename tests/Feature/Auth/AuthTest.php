<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_be_login(): void
    {
        $contraseña="12345";
        $user=User::factory()->create([
            'password' => Hash::make($contraseña)
        ]);
        $data=[
            "email"=>$user->email,
            "password"=>$contraseña
        ];
        $response=$this->postJson('api/v1/auth/login',$data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message','statusCode','error','data'
        ]);
        $this->assertAuthenticatedAs($user);
    }
    public function test_can_be_login_wrong_credentials(): void
    {
        $contraseña="12345";
        $user=User::factory()->create([
            'password' => Hash::make($contraseña)
        ]);
        $data=[
            "email"=>$user->email,
            "password"=>"contraseña incorrecta"
        ];
        $response=$this->postJson('api/v1/auth/login',$data);
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $this->assertGuest();
    }
    public function test_can_be_login_validation_error(): void
    {

        $data=[
            "email"=>"",
            "password"=>""
        ];
        $response=$this->postJson('api/v1/auth/login',$data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message"=>"Error de validación",
            "statusCode"=>422,
            "error"=>true
        ]);
        $response->assertJsonPath('data.email',['El campo correo electrónico es obligatorio.']);
        $response->assertJsonPath('data.password',['El campo contraseña es obligatorio.']);
    }
    public function test_can_be_register(): void
    {
        $data=[
            'firstname' => 'testuno',
            'lastname' => 'unounouno',
            'email' => 'test@hotmail.com',
            'password' => '12345678'
        ];
        $response=$this->postJson('api/v1/auth/register',$data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $this->assertDatabaseHas('users', [
            'firstname' => 'testuno',
            'lastname' => 'unounouno',
            'email' => 'test@hotmail.com',
        ]);
    }
    public function test_can_be_register_validation_error(): void
    {

        $response=$this->postJson('api/v1/auth/register',[
            "firstname"=>"jean pierre"
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
        $response->assertJson([
            "message"=>"Error de validación",
            "statusCode"=>422,
            "error"=>true
        ]);
        $response->assertJsonPath('data.lastname',["El campo lastname es obligatorio."]);
        $response->assertJsonPath('data.email',["El campo correo electrónico es obligatorio."]);
        $response->assertJsonPath('data.password',["El campo contraseña es obligatorio."]);

    }
    public function test_can_be_refresh_token(): void
    {

         $user = User::factory()->create();

         $token = JWTAuth::fromUser($user);

         $response = $this->withHeaders([
             'Authorization' => 'Bearer ' . $token,
         ])->postJson('/api/v1/auth/refresh');

         // Verificar la respuesta
         $response->assertStatus(200);
         $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data'
        ]);
         $response->assertJson([
             'message' => 'Token refrescado exitosamente',
         ]);

         // Verificar que el token nuevo es diferente al anterior
         $newToken = $response->json('token');
         $this->assertNotEquals($token, $newToken);

    }
}
