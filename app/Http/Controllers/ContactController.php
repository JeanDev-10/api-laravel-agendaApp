<?php

namespace App\Http\Controllers;

use App\Http\Resources\contact\ContactResource;
use App\Repository\Auth\AuthRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Responses\ApiResponses;
use App\Http\Requests\Contact\ContactRegisterRequest;
use App\Http\Requests\Contact\ContactUpdateRegisterRequest;
use App\Repository\Contact\ContactRepository;


use Exception;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private ContactRepository $contactRepository;
    private AuthRepository $authRepository;

    public function __construct(ContactRepository $contactRepository, AuthRepository $authRepository)
    {
        $this->contactRepository = $contactRepository;
        $this->authRepository = $authRepository;
    }

    public function index()
    {

        try {
            $contacts = $this->contactRepository->index();
            return ApiResponses::succes('Lista de contactos de un usuario.', 200, ContactResource::collection($contacts));

        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRegisterRequest $request)
    {
        try {
            $this->contactRepository->store($request);
            return ApiResponses::succes('Se ha creado exitosamente el contacto', 201);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (QueryException $e) {
            return ApiResponses::error("No puedes crear otro contacto con el mismo número", 422, ["message" => "No puedes crear otro contacto con el mismo número"]);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $contact = $this->contactRepository->show($id);
            return ApiResponses::succes('Mostrando Contacto', 200, new ContactResource($contact));

        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactUpdateRegisterRequest $request, $id)
    {
        try {
            $contacto = $this->contactRepository->show($id);
            $user = $this->authRepository->userProfile();
            if (!($contacto->user_id == $user->id)) {
                throw new AuthorizationException();
            }
            unset($contacto['encrypted_id']);


            $this->contactRepository->update($contacto, $request);

            return ApiResponses::succes('Se actualizó correctamente el contacto', 202);

        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (QueryException $e) {
            return ApiResponses::error("No puedes crear otro contacto con el mismo número", 422, ["message" => "Ya tienes un contacto con ese número", $e]);
        } catch (AuthorizationException $e) {
            return ApiResponses::error('No estás autorizado para realizar esta acción.', 403);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $contacto = $this->contactRepository->show($id);
            $user = $this->authRepository->userProfile();
            if (!($contacto->user_id == $user->id)) {
                throw new AuthorizationException();
            }
            $this->contactRepository->delete($contacto);
            return ApiResponses::succes('Se borró correctamente el contacto.', 200);

        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (AuthorizationException $e) {
            return ApiResponses::error('No estás autorizado para realizar esta acción.', 403);
        } catch (Exception $e) {
            return ApiResponses::error('Ha ocurrido un error', $e->getMessage(), 500);
        }
    }
}
