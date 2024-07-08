<?php

namespace App\Http\Requests\favorite;

use App\Http\Responses\ApiResponses;
use App\Models\Contact;
use App\Models\Favorite;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     title="FavoriteRegisterRequest",
 *     description="Request schema for registering a favorite contact",
 *     required={
 *         "contact_id"
 *     },
 * )
 */

class FavoriteRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contact_id' => [
                'required',

            ],
        ];
    }
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userId = auth()->user()->id;
            $contactId = $this->input('contact_id');

            if (Favorite::where('user_id', $userId)->where('contact_id',Crypt::decrypt($contactId))->exists()) {
                $validator->errors()->add('contact_id', 'Este contacto ya está en tu lista de favoritos.');
            }
        });
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            ApiResponses::error('Error de validación',422,$errors)
        );
    }
}
