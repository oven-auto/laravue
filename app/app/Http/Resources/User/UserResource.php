<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstname' => $this->name,
            'email' => $this->email,
            'lastname' => $this->lastname,
            'phone' => \StrHelp::phoneMask($this->phone),
            'role' => $this->role->name
        ];
    }
}
