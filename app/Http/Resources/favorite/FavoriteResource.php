<?php

namespace App\Http\Resources\favorite;

use App\Http\Resources\contact\ContactResource;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="FavoriteResource",
 *     title="Favorite Resource",
 *     description="Favorite resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="ID of the favorite (encrypted)",
 *         example="eyJpdiI6IjZkZ3BMRnl2Nlducmd4RWlCNDAxZkE9PSIsInZhbHVlIjoianpyOEorMWdwcmlRaWJPcUtlcFd0QT09IiwibWFjIjoiNTE2MTkxZDA0YTgyNTI1OTkwN2IxMjJlMmM1ZjNhZTQwYjNlYjIxOTBhNTI2NDcwMDM2YjkwYWY0YjVmNzYwOCIsInRhZyI6IiJ9"
 *     ),
 *     @OA\Property(
 *         property="contact",
 *         title="Contact details",
 *         description="Details of the contact that was favorited",
 *         @OA\Property(
 *             property="id",
 *             type="string",
 *             description="ID of the contact (encrypted)",
 *             example="eyJpdiI6Im53enBpS0xGZjF6VFUyaG1aQ2E3UFE9PSIsInZhbHVlIjoiaVJDdHZrZitLZ09HQ2YxMm84OUYvUT09IiwibWFjIjoiYmM2YzdlNjI1MjA4NjViOWJiYmIwMDBhN2U3ZjYyOGFmNzM3ZGY4YzJkZDI2ZjViYTY1NDkxNGIzY2I2YTM2YyIsInRhZyI6IiJ9"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="Name of the contact",
 *             example="Juanito"
 *         ),
 *         @OA\Property(
 *             property="phone",
 *             type="string",
 *             description="Phone number of the contact",
 *             example="0993954831"
 *         ),
 *         @OA\Property(
 *             property="nickname",
 *             type="string",
 *             description="Nickname of the contact",
 *             example=null
 *         ),
 *     ),
 * )
 */


class FavoriteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->encrypted_id,
            'contact' => new ContactResource($this->whenLoaded('Contact')),
        ];
    }
}
