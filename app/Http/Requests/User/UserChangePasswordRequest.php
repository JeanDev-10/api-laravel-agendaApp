<?php

namespace App\Http\Requests\User;

use App\Http\Responses\ApiResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
/**
 * @OA\Schema(
 *     schema="UserChangePasswordRequest",
 *     title="User Change Password Request",
 *     required={"password", "new_password", "new_password_confirmation"},
 *     @OA\Property(property="password", type="string", example="current_password"),
 *     @OA\Property(property="new_password", type="string", example="new_password"),
 *     @OA\Property(property="new_password_confirmation", type="string", example="new_password"),
 * )
 */
class UserChangePasswordRequest extends FormRequest
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
            'password' => 'required|min:3|max:10',
            'new_password' => 'required|min:3|max:10|confirmed',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            ApiResponses::error('Error de validación',422,$errors)
        );
    }
}
