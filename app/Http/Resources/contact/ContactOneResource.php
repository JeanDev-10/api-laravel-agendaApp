<?php

namespace App\Http\Resources\contact;

use App\Http\Resources\favorite\FavoriteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

/**
 * @OA\Schema(
 *     schema="ContactOneResource",
 *     title="ContactOne Resource",
 *     description="ContactOne resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="Encrypted ID of the contact",
 *         example="eyJpdiI6IjZkZ3BMRnl2Nlducmd4RWlCNDAxZkE9PSIsInZhbHVlIjoianpyOEorMWdwcmlRaWJPcUtlcFd0QT09IiwibWFjIjoiNTE2MTkxZDA0YTgyNTI1OTkwN2IxMjJlMmM1ZjNhZTQwYjNlYjIxOTBhNTI2NDcwMDM2YjkwYWY0YjVmNzYwOCIsInRhZyI6IiJ9"
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
 *     ),
 *     @OA\Property(
 *         property="favoritos",
 *         type="object",
 *         description="Favorite contact details",
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="ID of the favorite contact",
 *             example=5
 *         )
 *     )
 * )
 */

class ContactOneResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' =>  $this->encrypted_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'nickname' => $this->nickname,
            'favoritos' =>$this->whenLoaded('Favoritos', function () {
                return $this->favoritos ? [
                    'id' => Crypt::encrypt($this->favoritos->id),
                ] : null;
            }),
        ];
    }
}
