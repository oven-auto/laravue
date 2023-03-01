<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientListResource extends JsonResource
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
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'fathername' => $this->fathername,
            'emails' => $this->emails->count() ? '<V> Есть адрес электронной почты' : null,
            'phones' => $this->phones->map(function($item){
                return $item->hidden_phone;
            }),
            'worksheet' => $this->latest_worksheet->id,
            'loyalty' => $this->loyalty(),
        ];
    }
}
