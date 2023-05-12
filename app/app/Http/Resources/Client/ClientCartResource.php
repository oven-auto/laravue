<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $phones = $this->phones->map(function($item){
            return \StrHelp::phoneMask($item->phone);
        });

        $emails = $this->emails->map(function($item){
            return $item->email;
        });

        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->full_name,
                'phones' => $this->phones->count() ? $phones : [],
                'emails' => $this->emails->count() ? $emails : [],
                'zone' => $this->zone->name ?? '',
                'critical' => $this->critical(),
                'initials' => $this->initials(),
            ],
            'success' => 1
        ];
    }
}
