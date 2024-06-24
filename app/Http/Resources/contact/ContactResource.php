<?php

namespace App\Http\Resources\contact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->encrypted_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'nickname' => $this->nickname,
        ];
    }
}
