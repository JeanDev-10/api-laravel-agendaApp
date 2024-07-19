<?php

namespace App\Http\Controllers;

use App\Http\Requests\favorite\FavoriteRegisterRequest;
use App\Http\Resources\favorite\FavoriteResource;
use App\Http\Resources\paginate\PaginateResource;
use App\Http\Responses\ApiResponses;
use App\Repository\Favorite\FavoriteRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * @group Favorites
 *
 * APIs for managing user favorites.
 */
class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private FavoriteRepository $favoritesRepository;
    public function __construct(FavoriteRepository $favoritesRepository)
    {
        $this->favoritesRepository = $favoritesRepository;
    }
    /**
     * @OA\Get(
     *     path="/favorite",
     *     summary="Obtiene la lista de favoritos del usuario autenticado",
     *     tags={"Favorites"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filtro por nombre del contacto",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="Filtro por número de teléfono del contacto",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="nickname",
     *         in="query",
     *         description="Filtro por apodo del contacto",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de favoritos obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/FavoriteResource")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 ref="#/components/schemas/PaginationMeta"
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 ref="#/components/schemas/PaginationLinks"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener la lista de favoritos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Ha ocurrido un error: [detalles del error]"
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {

        try {
            $filters = $request->only(['name', 'phone', 'nickname']);
            $favorites = $this->favoritesRepository->index($filters);
            return ApiResponses::successs('Lista de favoritos de un usuario.', 200, new PaginateResource($favorites));
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/favorite",
     *     summary="Store a new favorite contact",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FavoriteRegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Favorite contact successfully stored",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Se ha creado añadido exitosamente el contacto a favoritos")
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
     *         description="Resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El recurso solicitado no existe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to add this contact to favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No estás autorizado para añadir este contacto a favoritos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: [detalles del error]")
     *         )
     *     )
     * )
     */
    public function store(FavoriteRegisterRequest $request)
    {
        try {
            if (Gate::denies('store', $request->contact_id)) {
                throw new AuthorizationException();
            }
            $this->favoritesRepository->store($request);
            return ApiResponses::successs('Se ha creado añadido exitosamente el contacto a favoritos', 201);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error("El recurso solicitado no existe", 404);
        } catch (AuthorizationException $e) {
            return ApiResponses::error("No estás autorizado para añadir este contacto a favoritos", 403);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/favorite/{id}",
     *     summary="Show a favorite contact",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the favorite contact"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite contact found",
     *         @OA\JsonContent(ref="#/components/schemas/FavoriteResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favorite contact not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se ha encontrado el contacto favorito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to view this favorite contact",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No estás autorizado para ver este contacto favorito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: [detalles del error]")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $favorite = $this->favoritesRepository->show($id);
            if (Gate::denies('show', $favorite)) {
                throw new AuthorizationException();
            }
            return ApiResponses::successs('Contacto favorito', 200, new FavoriteResource($favorite));
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error("No se ha encontrado el contacto favorito", 404);
        } catch (AuthorizationException $e) {
            return ApiResponses::error("No estás autorizado para ver este contacto favorito", 403);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/favorite/{id}",
     *     summary="Delete a favorite contact",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the favorite contact to delete"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite contact deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contacto favorito Eliminado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favorite contact not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se ha encontrado el contacto favorito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to delete this favorite contact",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No estás autorizado para elimiinar este contacto favorito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error: [detalles del error]")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $favorite = $this->favoritesRepository->showById($id);
            if (Gate::denies('delete', $favorite)) {
                throw new AuthorizationException();
            }
            $this->favoritesRepository->delete($favorite);
            return ApiResponses::successs('Contacto favorito Eliminado', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error("No se ha encontrado el contacto favorito", 404);
        } catch (AuthorizationException $e) {
            return ApiResponses::error("No estás autorizado para elimiinar este contacto favorito", 403);
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }
}
