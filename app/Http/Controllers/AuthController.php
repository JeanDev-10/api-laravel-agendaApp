<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Http\Resources\user\UserResource;
use App\Repository\Auth\AuthRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\ApiResponses;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Info(
 *             title="AppAgendaApi",
 *             version="1.0",
 *             description="Lista de Endspoints"
 * )
 *
 * @OA\Server(url="http://localhost:8000/api/v1/")
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Token de autenticación",
 *     name="BearerToken",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * )
 */

class AuthController extends Controller
{

    private AuthRepository $authRepository;
    public function __construct(
        AuthRepository $authRepository,
    ) {
        $this->authRepository = $authRepository;
    }


    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user",
     *     description="Endpoint to register a new user",
     *     operationId="register",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firstname", "lastname", "email", "password"},
     *             @OA\Property(property="firstname", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successsfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario creado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: {error_message}")
     *         )
     *     )
     * )
     */
    public function register(AuthRegisterRequest $request)
    {
        try {
            $this->authRepository->register($request);
            return ApiResponses::successs("Usuario creado correctamente", 201);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login a user",
     *     description="Endpoint to login a user",
     *     operationId="login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No existe ese registro")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: {error_message}")
     *         )
     *     )
     * )
     */
    public function login(AuthLoginRequest $request)
    {
        try {
            return $this->authRepository->login($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (ModelNotFoundException) {
            return ApiResponses::error("No existe ese registro", 404);
        } catch (JWTException $e) {
            return ApiResponses::error('No se pudo crear el token' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/auth/profile",
     *     summary="Get user profile",
     *     description="Endpoint to get user profile information",
     *     operationId="userProfile",
     *     tags={"Auth"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response=200,
     *         description="successsful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Perfil de usuario"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: {error_message}")
     *         )
     *     )
     * )
     */
    public function userProfile()
    {
        try {
            $user = $this->authRepository->userProfile();
            return ApiResponses::successs("Perfil de usuario", 200, new UserResource($user));
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout a user",
     *     description="Endpoint to logout a user",
     *     operationId="logout",
     *     tags={"Auth"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response=200,
     *         description="successsful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cierre de sesión exitoso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: {error_message}")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            $this->authRepository->logout();
            return ApiResponses::successs("Cierre de sesión exitoso", 200);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Refresh JWT token",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Token refrescado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refrescado exitosamente"),
     *             @OA\Property(property="token", type="string", example="new_jwt_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token has expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Token has expired")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ha ocurrido un error:",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ha ocurrido un error:")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function refresh()
    {
        try {
            $newToken = $this->authRepository->refresh();
            return ApiResponses::successs("Token refrescado exitosamente", 200, $newToken);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
}
