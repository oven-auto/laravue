<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientListResource extends JsonResource
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
            'name' => $this->full_name ? $this->full_name : 'ФИО неизвестно',
            'isname' => $this->full_name ? 1 : 0,
            'isemail' => $this->emails->count() ? 1 : 0,
            'phones' => $this->phones->map(function($item){
                return $item->hidden_phone;
            }),
            'trafic' => $this->latest_worksheet->trafic->id,
            'worksheet' => $this->latest_worksheet->id,
            'notification' => rand(0,1),
            'loyalty' => $this->loyalty(),
            'created_at' => $this->created_at->format('d.m.Y'),
            'action_at' => $this->latest_worksheet->created_at ? $this->latest_worksheet->created_at->format('d.m.Y') : ''
        ];
    }
}
