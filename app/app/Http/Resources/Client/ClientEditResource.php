<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->id)
            return [
                'data' => [
                    'id'                        => $this->id,
                    'firstname'                  => $this->firstname,
                    'lastname'                  => $this->lastname,
                    'fathername'          => $this->fathername,
                    'email'                   => $this->email,
                    'phone'                   => $this->phone,
                ],
                'status' => 1,
                'message' => $this->isCreate() ? 'Клиент добавлен' : 'Клиент изменен'
            ];
        else
            return [
                'status' => 0,
                'message' => 'Такой Клиент не существует'
            ];
    }
}
