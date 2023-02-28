<?php

namespace App\Http\Resources\Client\Car;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientCarResource extends JsonResource
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
            'brand' => $this->brand->name,
            'mark' => $this->mark->name,
            'bodywork' => $this->bodywork->name,
            'odometer' => $this->odometer,
            'year' => $this->year,
            'register_plate' => $this->register_plate,
            'vin' => $this->vin
        ];
    }
}
