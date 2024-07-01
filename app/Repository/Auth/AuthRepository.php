<?php

namespace App\Repository\Auth;

use App\Interfaces\Auth\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponses;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements AuthInterface
{
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return ApiResponses::error("Credenciales incorrectas", 401, ["message" => "Credenciales incorrectas"]);
        }
        return ApiResponses::successs("Usuario logeado exitosamente", 200, $token);
    }
    public function register(Request $request)
    {
        User::create([
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);
    }
    public function userProfile()
    {
        return auth()->user();
    }
    public function logout()
    {
        auth()->logout();
    }
}
