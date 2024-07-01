<?php

namespace App\Http\Requests\Contact;

use App\Http\Responses\ApiResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Auth;

/**
 * @OA\Schema(
 *     schema="ContactRegisterRequest",
 *     type="object",
 *     title="Contact Register Request",
 *     description="Request body for creating a new contact",
 *     required={"name", "phone"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Contact name",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Contact phone number",
 *         example="123456789"
 *     ),
 *     @OA\Property(
 *         property="nickname",
 *         type="string",
 *         description="Nickame phone",
 *         example="Jhon3"
 *     )
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
