<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Resources\Json\JsonResource;

class CarEditResource extends JsonResource
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
                    'brand_id'                  => $this->brand_id,
                    'color_id'                  => $this->mark_color_id,
                    'complectation_id'          => $this->complectation_id,
                    'mark_id'                   => $this->mark_id,
                    'year'                      => $this->year,
                    'device_price'              => $this->device_price,
                    'device_cost'               => $this->device_cost,
                    'vin'                       => $this->vin,
                    'packs'                     => $this->packs->pluck('id'),
                    'devices'                   => $this->devices->pluck('id'),
                    'marker_id'                 => $this->marker->marker_id,
                    'delivery_stage_id'         => $this->delivery->delivery_stage_id,
                    'delivery_type_id'          => $this->delivery->delivery_type_id ?? '',
                    'production_at'             => $this->production->production_at,
                    'client'                    => '',//$this->client()->exists() ? $this->client : '',
                    'purchase'                  => $this->purchase,
                ],
                'status' => 1,
                'message' => $this->isCreate() ? 'Автомобиль создан' : 'Автомобиль изменен'
            ];
        else
            return [
                'status' => 0,
                'message' => 'Такой Автомобиль не существует'
            ];
    }
}
