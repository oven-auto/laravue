<?php

namespace App\Http\Resources\Client\Event\Reporter;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientEventReporterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' =>$this->cut_name,
            'created_at' => (isset($this->pivot) && $this->pivot->created_at) ? $this->pivot->created_at->format('d.m.Y (H:i)') : ''
        ];
    }
}
