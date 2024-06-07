<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Responses\ApiResponses;
use Illuminate\Validation\Rule;
use App\Models\Contact;
use Exception;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {


            $request->validate([
                'name' => 'required|string|min:3|max:255',
                'phone' => [
                    'required',
                    'string',
                    'min:10',
                    'max:10',
                    Rule::unique('contacts')->where(function ($query) use ($request) {
                        return $query->where('user_id', $request->user_id);
                    }),
                ],
                'nickname' => 'nullable|string|min:3|max:255',
                'user_id' => 'required|exists:users,id'
            ]);


            Contact::create($request->all());
            
            return response()->json([
                'success' => true, 
                'message' => 'Se ha creado exitosamente el contacto',
                'statusCode' => 201
            ], 201);

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        try {


            $contacto = Contact::where(['id' => $id])->firstOrFail();

            $request->validate([
                'name' => 'required|string|min:3|max:255',
                'phone' => [
                    'required',
                    'string',
                    'min:10',
                    'max:10',
                    Rule::unique('contacts')->ignore($contacto)
                ],
                'nickname' => 'nullable|string|min:3|max:255',
                'user_id' => 'required|exists:users,id'
            ]);

            $contacto->update($request->all());

            return response()->json([
                'success' => true,
                'msj' => 'Se actualizó correctamente el contacto',
                'statusCode' => 202
            ], 202);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Contacto no encontrado',
                'statusCode' => 404
            ], 404);
        } catch (ValidationException $e){
            $errors = $e->validator->errors()->toArray();
            return ApiResponses::error("Error de validación", 422, $errors);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
    }
}
