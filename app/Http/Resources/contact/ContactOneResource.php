<?php

namespace App\Http\Resources\contact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;





class ContactOneResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' =>  $this->encrypted_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'nickname' => $this->nickname,
            'favoritos' =>[
                "id"=>$this->favoritos->id
            ],
        ];
    }
}
