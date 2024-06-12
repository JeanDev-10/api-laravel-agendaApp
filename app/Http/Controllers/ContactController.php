<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

     public function __construct(ContactRepository $contactRepository){
        $this->contactRepository = $contactRepository;
     }
     
    public function index()
    {

        try {
            return $this->contactRepository->index();
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
            return $this->contactRepository->store($request);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
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
            return $this->contactRepository->show($id);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (Exception $e){
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactUpdateRegisterRequest $request,  $id)
    {
        try {
            return $this->contactRepository->update($request, $id);
        } catch (ModelNotFoundException $e) {
            return ApiResponses::error('Contacto no encontrado', 404);
        } catch (ValidationException $e){
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        } catch (Exception $e){
            return ApiResponses::error("Ha ocurrido un error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
           return $this->contactRepository->delete($id);
        } catch (ModelNotFoundException $e){
            return ApiResponses::error('Contacto no encontrado', 404);
        }
        catch (Exception $e) {
           return ApiResponses::error('Ha ocurrido un error', $e->getMessage(), 500);
        }
    }
}
