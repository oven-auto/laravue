<?php

namespace App\Http\Resources\Credit;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CreditCollection extends ResourceCollection
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
            'status' => $this->collection->count()
        ];
    }
}
