<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Repository\Auth\AuthRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\ApiResponses;

class AuthController extends Controller
{

    private AuthRepository $authRepository;
    public function __construct(
        AuthRepository $authRepository,
    ) {
        $this->authRepository=$authRepository;
    }
    public function register(AuthRegisterRequest $request)
    {
        try {
            return $this->authRepository->register($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }


    public function login(AuthLoginRequest $request)
    {
        try {
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
