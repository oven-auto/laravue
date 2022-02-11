<?php

namespace App\Http\Resources\Mark;

use Illuminate\Http\Resources\Json\JsonResource;

class MarkIconResource extends JsonResource
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
            'image' => $this->image_date,
            'mark_id' => $this->mark_id
        ];
    }
}
