<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CarListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'cars' => $this->collection,
            'status' => $this->collection->count() ? 1 : 0,
            'message' => 'Найдено '.$this->collection->count().' автомобилей',
            'count' => $this->collection->count(),
            'total' => $this->total(),
            'last_page' => $this->lastPage()
        ];
    }
}
