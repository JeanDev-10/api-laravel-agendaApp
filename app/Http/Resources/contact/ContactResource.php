<?php

namespace App\Http\Resources\contact;

use Illuminate\Http\Resources\Json\JsonResource;




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
