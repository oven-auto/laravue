<?php

namespace App\Http\Resources\Worksheet\Reserve;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReserveItemResource extends JsonResource
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
            'type' => [
                'id' => $this->type->id,
                'name' => $this->type->name,
                'returnable' => $this->type->returnable,
            ],
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->cut_name,
            ],
            'sum' => $this->sum ? [
                'amount' => $this->sum->amount,
                'author' => $this->sum->author->cut_name,
                'created' => $this->sum->updated_at->format('d.m.Y'),
            ] : [],
            'reparation' => $this->reparation ? [
                'amount' => $this->reparation->amount,
                'author' => $this->reparation->author->cut_name,
                'created' => $this->reparation->updated_at->format('d.m.Y'),
            ] : [],
            'reparation_date' => $this->reparation_date ? [
                'date' => $this->reparation_date->date_at->format('d.m.Y'),
                'author' => $this->reparation_date->author->cut_name,
                'created' => $this->reparation_date->updated_at->format('d.m.Y'),
            ] : [],
            'base' => $this->base ? [
                'base' => $this->base->base,
                'author' => $this->base->author->cut_name,
                'created' => $this->base->updated_at->format('d.m.Y'),
            ] : [],
        ];
    }
}
