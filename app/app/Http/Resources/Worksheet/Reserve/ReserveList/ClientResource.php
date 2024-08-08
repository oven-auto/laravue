<?php

namespace App\Http\Resources\Worksheet\Reserve\ReserveList;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'phones' => $this->phones->first() ? $this->phones->first()->phone : '',
            'zone' => $this->zone->name ?? '',
        ];
    }
}
