<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            return response()->json([
                'success' => false, 
                'message' => $e->errors(),
                'statusCode' => 409
            ], 409);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
    }
}
