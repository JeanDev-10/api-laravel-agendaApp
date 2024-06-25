<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
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
    private ContactRepository $contactRepository;
    private AuthRepository $authRepository;

    public function __construct(ContactRepository $contactRepository, AuthRepository $authRepository)
    {
        $this->contactRepository = $contactRepository;
        $this->authRepository = $authRepository;
    }

    /**
     * @OA\Get(
     *     path="/contacts",
     *     summary="Get list of contacts",
     *     description="Returns a list of contacts for a user.",
     *     tags={"Contacts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of contacts.",
     *         @OA\JsonContent(type="array", @OA\Items(ref="App\Http\Resources\contact\ContactResource"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function index()
    {
        try {
            $contacts = $this->contactRepository->index();
            return ApiResponses::success('Lista de contactos de un usuario.', 200, ContactResource::collection($contacts));
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/contacts",
     *     summary="Create a new contact",
     *     description="Stores a newly created contact in the database.",
     *     tags={"Contacts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="App\Http\Resources\contact\ContactRegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact created successfully."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function store(ContactRegisterRequest $request)
    {
        try {
            $this->contactRepository->store($request);
            return ApiResponses::success('Se ha creado exitosamente el contacto', 201);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (QueryException $e) {
            return ApiResponses::error("No puedes crear otro contacto con el mismo número", 422, ["message" => "Ya tienes un contacto con ese número."]);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/contacts/{id}",
     *     summary="Get contact details",
     *     description="Returns details of a specific contact.",
     *     tags={"Contacts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact details.",
     *         @OA\JsonContent(ref="App\Http\Resources\contact\ContactRegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $contact = $this->contactRepository->show($id);
            return ApiResponses::success('Mostrando Contacto', 200, new ContactResource($contact));
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/contacts/{id}",
     *     summary="Update an existing contact",
     *     description="Updates the details of an existing contact.",
     *     tags={"Contacts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="App\Http\Resources\contact\Contact")
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Contact updated successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error."
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function update(ContactUpdateRegisterRequest $request, $id)
    {
        try {
            $contact = $this->contactRepository->show($id);
            $user = $this->authRepository->userProfile();
            
            if (!($contact->user_id == $user->id)) {
                throw new AuthorizationException();
            }
            
            $this->contactRepository->update($contact, $request);

            return ApiResponses::success('Se actualizó correctamente el contacto', 202);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (QueryException $e) {
            return ApiResponses::error("No puedes crear otro contacto con el mismo número", 422, ["message" => "Ya tienes un contacto con ese número."]);
        } catch (AuthorizationException $e) {
            return ApiResponses::error("No estás autorizado para actualizar este contacto", 403);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/contacts/{id}",
     *     summary="Delete a contact",
     *     description="Deletes a specific contact.",
     *     tags={"Contacts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found."
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $contact = $this->contactRepository->show($id);
            $user = $this->authRepository->userProfile();
            
            if (!($contact->user_id == $user->id)) {
                throw new AuthorizationException();
            }
            
            $this->contactRepository->delete($contact);
            
            return ApiResponses::success('Se borró correctamente el contacto.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (AuthorizationException $e) {
            return ApiResponses::error('No estás autorizado para realizar esta acción.', 403);
        } catch (Exception $e) {
            return ApiResponses::error('Ha ocurrido un error: ' . $e->getMessage(), 500);
        }
    }
}
