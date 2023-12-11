<?php

namespace App\Http\Resources\Client\Car;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientCarEditResource extends JsonResource
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
                'brand_id' => $this->brand_id,
                'mark_id' => $this->mark_id,
                'body_work_id' => $this->body_work_id,
                'vin' => $this->vin ? $this->vin : '',
                'odometer' => $this->odometer ? $this->odometer : '',
                'register_plate' => $this->register_plate ? $this->register_plate : '',
                'year' => $this->year ? $this->year : ''
            ],
            'success' => 1
        ];
    }
}
