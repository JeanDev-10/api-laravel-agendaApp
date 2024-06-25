<?php

namespace App\Http\Resources\contact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ContactResource",
 *     title="Contact Resource",
 *     description="Contact resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Id of the contact (encrypted)",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the contact",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the contact",
 *         example="123456789"
 *     ),
 *     @OA\Property(
 *         property="nickname",
 *         type="string",
 *         description="Nickname of the contact",
 *         example="Johnny"
 *     )
 * )
 */

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
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="ContactUpdateRegisterRequest",
 *     type="object",
 *     title="Contact Update Request",
 *     description="Request body for updating a contact",
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
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="Contact",
 *     type="object",
 *     title="Contact",
 *     description="Contact model",
 *     required={"id", "name", "phone"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Contact ID",
 *         example="1"
 *     ),
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
 *     )
 * )
 */
class ContactResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->encrypted_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'nickname' => $this->nickname,
        ];
    }
}
