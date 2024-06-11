<?php

namespace App\Http\Controllers;
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
        $this->userRepository=$userRepository;
    }
    public function changePassword(UserChangePasswordRequest $request){
        try{
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
    public function editProfile(UserEditProfileRequest $request){
        try{
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
