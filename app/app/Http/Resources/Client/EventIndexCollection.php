<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventIndexCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'success' => 1
        ];
    }
}
