<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserEditProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_user_edit_profile(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $data=[
            'firstName' => "jean pierre",
            "lastName"=>"rodriguez"
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->putJson('api/v1/auth/editProfile',$data);
        $response->assertStatus(200);
        $response->assertJson([
            "message"=>"Perfil actualizado exitosamente",
            "statusCode"=>200,
            "error"=>false
        ]);
        $this->assertDatabaseHas('users',$data);
        $this->assertDatabaseMissing('users',$user->toArray());
    }
    public function test_can_no_user_edit_profile_same_data(): void
    {
        $user = User::factory()->create([
            'firstname' => "jean pierre",
            "lastname"=>"rodriguez"
        ]);
        $token = JWTAuth::fromUser($user);
        $data=[
            'firstName' => "jean pierre",
            "lastName"=>"rodriguez"
        ];
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->putJson('api/v1/auth/editProfile',$data);
        $response->assertStatus(200);
        $response->assertJson([
            "message"=>"No hay cambios, perfil no fue actualizado",
            "statusCode"=>200,
            "error"=>false
        ]);
        $this->assertDatabaseHas('users',$user->toArray());
    }
    public function test_can_user_edit_profile_validation_error(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer $token"
        ])
            ->putJson('api/v1/auth/editProfile',[]);
        $response->assertStatus(422);
        $response->assertJson([
            "message"=>"Error de validación",
            "statusCode"=>422,
            "error"=>true
        ]);
        $response->assertJsonPath('data.firstName',['El campo first name es obligatorio.']);
        $response->assertJsonPath('data.lastName',['El campo last name es obligatorio.']);
        $this->assertDatabaseHas('users',$user->toArray());
        $this->assertDatabaseCount('users',1);
    }
}
