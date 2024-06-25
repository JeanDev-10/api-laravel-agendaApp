<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CheckThePasswordRequest;
use App\Http\Requests\User\UserChangePasswordRequest;
use App\Http\Requests\User\UserEditProfileRequest;
use App\Http\Responses\ApiResponses;
use App\Repository\User\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{
    private UserRepository $userRepository;
    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }


    /**
     * @OA\Post(
     *     path="/auth/changePassword",
     *     summary="Change user password",
     *     description="Endpoint to change user password.",
     *     operationId="changePassword",
     *     tags={"User"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Password change data",
     *         @OA\JsonContent(
     *             required={"password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="password", type="string", format="password", example="old_password"),
     *             @OA\Property(property="new_password", type="string", format="password", example="new_password"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="new_password"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contraseña cambiada exitosamente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No existe el usuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object", example={"password": {"La contraseña actual es incorrecta"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: Internal Server Error")
     *         )
     *     )
     * )
     */
    public function changePassword(UserChangePasswordRequest $request)
    {
        try {
            return $this->userRepository->changePassword($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (ModelNotFoundException) {
            return ApiResponses::error("No existe el usuario", 404);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
 * @OA\Post(
 *     path="/auth/check-password",
 *     summary="Check the user's password",
 *     description="Endpoint to verify if the provided password is correct",
 *     operationId="checkThePassword",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"password"},
 *             @OA\Property(property="password", type="string", minLength=3, maxLength=10, example="yourpassword")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password is correct",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Contraseña correcta!"),
 *             @OA\Property(property="status", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Incorrect password",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Contraseña actual incorrecta"),
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="errors", type="object", example={"message":"Contraseña incorrecta"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Error de validación"),
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="errors", type="object", example={"password":{"El campo contraseña es obligatorio"}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="No existe el usuario"),
 *             @OA\Property(property="status", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: {error_message}"),
 *             @OA\Property(property="status", type="integer", example=500)
 *         )
 *     )
 * )
 */
    public function checkThePassword(CheckThePasswordRequest $request)
    {
        try {
            return $this->userRepository->CheckThePassword($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (ModelNotFoundException) {
            return ApiResponses::error("No existe el usuario", 404);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/editProfile",
     *     summary="Edit user profile",
     *     description="Endpoint to edit user profile information.",
     *     operationId="editProfile",
     *     tags={"User"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Profile update data",
     *         @OA\JsonContent(
     *             required={"firstName", "lastName"},
     *             @OA\Property(property="firstName", type="string", example="John"),
     *             @OA\Property(property="lastName", type="string", example="Doe"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Perfil actualizado exitosamente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No existe el usuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object", example={"firstName": {"El campo nombre es obligatorio"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: Internal Server Error")
     *         )
     *     )
     * )
     */
    public function editProfile(UserEditProfileRequest $request)
    {
        try {
            return $this->userRepository->editProfile($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (ModelNotFoundException) {
            return ApiResponses::error("No existe el usuario", 404);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
}
