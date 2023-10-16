<?php

namespace App\Http\Resources\Client\Event;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'success' => 1,
            'data' => [
                'reporters' => $this->reporters->map(function($item)
                {
                    return [
                        'id' => $item->id,
                        'name' =>$item->cut_name,
                    ];
                }),
            ],
        ];
    }
}
