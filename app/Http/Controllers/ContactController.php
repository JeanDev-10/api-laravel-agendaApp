<?php

namespace App\Http\Controllers;

use App\Http\Resources\contact\ContactResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Responses\ApiResponses;
use App\Http\Requests\Contact\ContactRegisterRequest;
use App\Http\Requests\Contact\ContactUpdateRegisterRequest;
use App\Http\Resources\contact\ContactOneResource;
use App\Http\Resources\paginate\PaginateResource;
use App\Repository\Contact\ContactRepository;
use Exception;

class ContactController extends Controller
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }


    public function index()
    {
        try {
            $contacts = $this->contactRepository->index();
            return ApiResponses::successs('Lista de contactos de un usuario.', 200, new PaginateResource($contacts));
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/contact",
     *     summary="Create a new contact",
     *     description="Stores a newly created contact in the database.",
     *     tags={"Contacts"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *             required={"name", "phone"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="phone", nullable=true ,type="string", example="0993854921"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact created successsfully.",
     *     @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Se ha creado exitosamente el contacto")
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
     *      @OA\Response(
     *        response=500,
     *        description="Internal server error",
     *        @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: {error_message}")
     *         )
     *      )
     * )
     */
    public function store(ContactRegisterRequest $request)
    {
        try {
            $this->contactRepository->store($request);
            return ApiResponses::successs('Se ha creado exitosamente el contacto', 201);
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
     *     path="/contact/{id}",
     *     summary="Get contact details",
     *     description="Returns details of a specific contact.",
     *     security={ {"bearerAuth": {} } },
     *     tags={"Contacts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact details.",
     *         @OA\JsonContent(
     *             required={"name", "phone"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="phone", type="string", example="0993854921"),
     *             @OA\Property(property="nickname", type="string", nullable=true, example="Jhon23")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contacto no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: {error_message}")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $contact = $this->contactRepository->show($id);
            $this->authorize('show', $contact);
            /* return ApiResponses::successs('Mostrando Contacto', 200, $contact); */
            return ApiResponses::successs('Mostrando Contacto', 200, new ContactOneResource($contact));
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (AuthorizationException $e) {
            return ApiResponses::error("No estás autorizado para ver este contacto", 403);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/contact/{id}",
     *     summary="Update an existing contact",
     *     description="Updates the details of an existing contact.",
     *     security={ {"bearerAuth": {} } },
     *     tags={"Contacts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *     @OA\JsonContent(
     *             required={"name", "phone"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="phone" ,type="string", example="0993854921"),
     *             @OA\Property(property="nickname" ,type="string", nullable=true ,example="Jhon123"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Contact updated successsfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contacto actualizado exitosamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contacto no encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación: {validation_errors}")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autorizado para realizar esta acción.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error interno del servidor.")
     *         )
     *     )
     * )
     */
    public function update(ContactUpdateRegisterRequest $request, $id)
    {
        try {
            $contact = $this->contactRepository->show($id);
            $this->authorize('update', $contact);
            unset($contact['encrypted_id']);
            $this->contactRepository->update($contact, $request);

            return ApiResponses::successs('Se actualizó correctamente el contacto', 202);
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
     *     path="/contact/{id}",
     *     summary="Delete a contact",
     *     description="Deletes a specific contact.",
     *     tags={"Contacts"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact deleted successsfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Se borró correctamente el contacto.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contacto no encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autorizado para realizar esta acción.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error interno del servidor.")
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            $contact = $this->contactRepository->show($id);
            $this->authorize('delete', $contact);
            $this->contactRepository->delete($contact);

            return ApiResponses::successs('Se borró correctamente el contacto.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (AuthorizationException $e) {
            return ApiResponses::error('No estás autorizado para realizar esta acción.', 403);
        } catch (Exception $e) {
            return ApiResponses::error('Ha ocurrido un error: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Restore deleted contacts for the logged-in user.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/contacts/restore",
     *     summary="Restore deleted contacts",
     *     security={ {"bearerAuth": {} } },
     *     tags={"Contacts"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Se restauró correctamente los contactos."
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No contacts to restore",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No hay contactos para restaurar"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Ha ocurrido un error: Internal Server Error"
     *             )
     *         )
     *     )
     * )
     */
    public function restoreContacts()
    {
        try {
            $contacts = $this->contactRepository->restoreContacts();
            if ($contacts == []) {
                throw new ModelNotFoundException();
            }
            return ApiResponses::successs('Se restauró correctamente los contactos.', 200, $contacts);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('No hay contactos para restaurar', 404);
        } catch (Exception $e) {
            return ApiResponses::error('Ha ocurrido un error: ' . $e->getMessage(), 500);
        }
    }
}
