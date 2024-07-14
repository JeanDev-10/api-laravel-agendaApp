<?php

namespace App\Http\Resources\contact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ContactResource",
 *     type="object",
 *     title="Contact Resource",
 *     properties={
 *         @OA\Property(
 *             property="id",
 *             type="string",
 *             example="encrypted_id"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="John Doe"
 *         ),
 *         @OA\Property(
 *             property="phone",
 *             type="string",
 *             example="1234567890"
 *         ),
 *         @OA\Property(
 *             property="nickname",
 *             type="string",
 *             example="Johnny"
 *         ),
 *         @OA\Property(
 *             property="favoritos",
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(
 *                     property="id",
 *                     type="integer",
 *                     example=1
 *                 )
 *             )
 *         )
 *     }
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
