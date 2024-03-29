<?php

namespace App\Http\Resources\Trafic;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TraficProductCollection extends ResourceCollection
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
            'data' => $this->collection,
            'success' => 1,
            'message' =>'Найдено '.$this->collection->count().' элементов',
            'count' => $this->collection->count(),
        ];
    }
}
