<?php

namespace App\Http\Controllers;

use App\Http\Repository\Auth\AuthRepository;
use App\Http\Responses\ApiResponses;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthRepository $authRepository,
    ) {
    }
    public function register(Request $request)
    {
        try {
            $request->validate([
                'firstname' => 'required|min:3',
                'lastname' => 'required|min:3',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:3|max:10'
            ]);
            return $this->authRepository->register($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $request->validate([
                "email" => "required|email",
                "password" => "required"
            ]);
            return $this->authRepository->login($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (ModelNotFoundException) {
            return ApiResponses::error("No existe ese registro", 404);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }

    }

    public function userProfile()
    {
        try {
            return $this->authRepository->userProfile();
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }

    }

    public function logout()
    {
        try {
            return $this->authRepository->logout();
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
}
