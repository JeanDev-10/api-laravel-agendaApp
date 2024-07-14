<?php

namespace App\Http\Resources\paginate;

use App\Http\Resources\contact\ContactResource;
use App\Http\Resources\favorite\FavoriteResource;
use App\Models\Contact;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=10),
 *     @OA\Property(property="path", type="string", example="http://localhost/api/contacts"),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="to", type="integer", example=10),
 *     @OA\Property(property="total", type="integer", example=100)
 * )
 */

class PaginateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->map(function ($item) {
            if ($item instanceof Contact) {
                return new ContactResource($item);
            } elseif ($item instanceof Favorite) {
                return new FavoriteResource($item);
            }
            dd($item);
            return null; // Handle other cases if necessary
        });
        return [
            'data' => $data,
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'path' => $this->path(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}
