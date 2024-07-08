<?php

namespace App\Http\Controllers;

use App\Http\Requests\favorite\FavoriteRegisterRequest;
use App\Http\Resources\favorite\FavoriteResource;
use App\Http\Responses\ApiResponses;
use App\Repository\Favorite\FavoriteRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
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
     * Display a listing of the resource.
     *
     * @return JsonResponse
     *
     * @throws Exception
     *
     * @OA\Get(
     *     path="/favorite",
     *     summary="Get all favorite contacts",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of favorite contacts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/FavoriteResource")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     ),
     * )
     */
    public function index()
    {

        try {
            $favorites = $this->favoritesRepository->index();
            return ApiResponses::successs('Lista de favoritos de un usuario.', 200, FavoriteResource::collection($favorites));
        } catch (Exception $e) {
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

      /**
     * Store a newly created resource in storage.
     *
     * @param FavoriteRegisterRequest $request
     * @return JsonResponse
     *
     * @throws Exception
     *
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
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to add this contact to favorites",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     ),
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
     * Display the specified resource.
     */

     /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     *
     * @throws Exception
     *
     * @OA\Get(
     *     path="/favorite/{id}",
     *     summary="Get a specific favorite contact",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the favorite contact",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite contact details",
     *         @OA\JsonContent(ref="#/components/schemas/FavoriteResource"),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to view this favorite contact",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favorite contact not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     ),
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
     * Remove the specified resource from storage.
     */

      /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     *
     * @throws Exception
     *
     * @OA\Delete(
     *     path="/favorite/{id}",
     *     summary="Delete a favorite contact",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the favorite contact",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite contact successfully deleted",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to delete this favorite contact",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favorite contact not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     ),
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
