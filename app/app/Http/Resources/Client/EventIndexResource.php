<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class EventIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $author = auth()->user();

        //auth()->user()->id == $this->event->author_id
        $canIChange = (
            (
                auth()->user()->role->permissions->contains('slug', 'cevent_show')
                && $this->event->executors->contains('id', auth()->user()->id)
            )
            || auth()->user()->role->permissions->contains('slug', 'cevent_show_alien')
            || auth()->user()->id == $this->event->author_id
        ) ? 1 : 0;

        return [
            'id' => $this->id,
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'date_at' => $this->date_at->format('d.m.Y'),
            // 'executors' => $this->event->executors->map(function($item) {
            //     return [
            //         'id' => $item->id,
            //         'name' => $item->cut_name,
            //     ];
            // }),
            'type' => $this->event->type->name,
            'client' => $this->event->client->full_name,
            'status' => $this->description->name,
            'processed_at' => $this->processed_at ? $this->processed_at->format('d.m.Y') : '',
            'completer' => $this->completer->cut_name,
            'client_type' => $this->event->client->type->name,
            'comment' => $this->lastcomment->created_at ? $this->lastcomment->created_at->format('d.m.Y (H:i)').' '.$this->lastcomment->text : '',
            //'author' => $this->event->author->cut_name,
            'executor' => $this->event->executor,
            'trafic' => $this->trafic->id ? $this->trafic->id : '',
            'client_id' => $this->event->client->id,
            'worksheet_id' => $this->trafic->worksheet->id,
            'group' => $this->event->group->name ?? '',
            'can_i_change' => $canIChange,
        ];
    }
}
