<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class EventSaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $params = [
            'date_at' => 1,
            'title' => 1,
            'group_id' => 1,
            'type_id' => 1,
            'text' => 1,
            'executors' => 1
        ];
        if(auth()->user()->id != $this->event->author_id)
            $params = [
                'executors' => 2,
                'text' => 1,
                'date_at' => 0,
                'title' => 0,
                'group_id' => 0,
                'type_id' => 0,
            ];

        return [
            'data' => [
                'created_at' => $this->event->created_at->format('d.m.Y'),
                'id' => $this->id,
                'client_id' => $this->event->client_id,
                'author_id' => $this->event->author_id,
                'title' => $this->event->title ?? '',
                'group_id' => $this->event->group_id,
                'type_id' => $this->event->type_id,
                'date_at' => $this->date_at ? $this->date_at->format('d.m.Y') : '',
                'client' => $this->event->client->full_name,
                'author' => $this->event->author->cut_name,
                'comments' => $this->event->comments->map(function($item) {
                    return [
                        'created_at' => $item->created_at->format('d.m.Y'),
                        'text' => $item->text,
                        'writer' => $item->author->cut_name,
                    ];
                }),
                'executors' => $this->event->executors->map(function($item){
                    return $item->id;
                }),
                'status' => $this->confirm,
                'fillable_properties' => $params,
                'method' => \Route::current()->methods(),
                'event_status_id' => $this->id
            ],
            'success' => 1,
        ];
    }
}
