<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mark\ColorImageResource;

class CarArchiveEditResource extends JsonResource
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
                    'id' => $this->id,
                    'brand' => $this->brand,
                    'color' => new ColorImageResource($this->color),
                    'complectation' => $this->complectation,
                    'delivery' => $this->delivery,
                    'device_price' => $this->device_price,
                    'mark' => $this->mark,
                    'packs' => $this->packs,
                    'price' => $this->price,
                    'production' => $this->production,
                    'vin' =>$this->vin,
                    'year' => $this->year,
                    'fixedprice' => $this->fixedprice,
                    'deleted_at' => $this->deleted_at,
                ],
                'status' => 1,
            ];
        else
            return [
                'status' => 0,
                'message' => 'Такой Автомобиль не существует'
            ];
    }
}
