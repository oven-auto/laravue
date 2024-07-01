<?php

namespace App\Http\Resources\Worksheet\Reserve;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReserveItemResource extends JsonResource
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
            'amount' => $this->amount,
            'name' => $this->name,
            'reserve_id' => $this->reserve_id,
            'author' => [
                'name' => $this->author->cut_name,
                'id' => $this->author->id
            ],

        ];
    }
}
