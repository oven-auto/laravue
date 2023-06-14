<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSaveResource extends JsonResource
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
            'data' => [
                'id' => $this->id,
                'firstname' => $this->name,
                'email' => $this->email,
                'lastname' => $this->lastname,
                'phone' => $this->phone,
                'role_id' => $this->role_id,
                'appeals' => $this->appeals,
                'structures' => $this->structures,
            ],
            'success' => 1,
            'manual' => 'Для пароля использовать поле password, для поля подтверждения пароля password_confirmation'
        ];
    }
}
