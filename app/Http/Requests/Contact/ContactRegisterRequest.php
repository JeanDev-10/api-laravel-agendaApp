<?php

namespace App\Http\Requests\Contact;

use App\Http\Responses\ApiResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="ContactRegisterRequest",
 *     title="Contact Register Request",
 *     required={"name", "phone"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="phone", type="string", example="1234567890"),
 *     @OA\Property(property="nickname", type="string", example="Johnny"),
 * )
 */
class ContactRegisterRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:10',
                'regex:/^[0-9]+$/',
                Rule::unique('contacts')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id)->whereNull('deleted_at');
                }),
            ],
            'nickname' => 'nullable|string|min:3|max:255'
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
