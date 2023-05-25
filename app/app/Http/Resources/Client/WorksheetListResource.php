<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class WorksheetListResource extends JsonResource
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
            'created_at' => $this->created_at->format('d.m.Y (H:i)'),
            'company' => $this->company->name,
            'structure' => $this->structure->name,
            'appeal' => $this->appeal->name,
            'status' => $this->status(),
            'client_id' => $this->client_id,
            'trafic_id' => $this->trafic_id
        ];
    }
}
