<?php

namespace App\Http\Resources\Motor;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TypeListCollection extends ResourceCollection
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
            'success' => $this->collection->count() ? 1 : 0,
            'message' => 'Найдено '.$this->collection->count().' типов моторов',
            'count' => $this->collection->count()
        ];
    }
}
