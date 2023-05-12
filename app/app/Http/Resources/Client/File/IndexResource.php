<?php

namespace App\Http\Resources\Client\File;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
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
            'client' => $this->client->full_name,
            'author' => $this->author->cut_name,
            'created_at' => $this->date,
            'title' => $this->title,
            'file' => \WebUrl::make_link($this->file),
        ];
    }
}
