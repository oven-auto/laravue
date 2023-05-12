<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class EventListResource extends JsonResource
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
            'title' => $this->event->title ?? '',
            'text' => $this->lastComment->text,
            'author' => $this->event->author->cut_name,
            'type' => $this->event->type->name,
            'date_at' => $this->date_at->format('d.m.Y'),
            'status' => $this->status,
            'property' => $this->event->group->name,

        ];
    }
}
