<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mark\ColorImageResource;

class CarListResource extends JsonResource
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
            'brand' => $this->brand,
            'color' => new ColorImageResource($this->color),
            'complectation' => $this->complectation,
            'delivery' => isset($this->delivery->id) ? $this->delivery : null,
            'device_price' => $this->device_price,
            'mark' => $this->mark,
            'packs' => $this->packs,
            'price' => $this->price,
            'production' => $this->production,
            'vin' =>$this->vin,
            'year' => $this->year,
            'marker' => $this->marker->name->name ? $this->marker->name->name.' ('.$this->marker->moderator->name.' '.$this->marker->change_date.')' : '',
            'client' => '',//$this->client()->exists() ? $this->client : '',
            'purchase' => $this->purchase,
        ];
    }
}
