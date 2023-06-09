<?php

namespace App\Http\Resources\Worksheet\Action;

use Illuminate\Http\Resources\Json\JsonResource;

class ActionStatusResource extends JsonResource
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
            'data' => [
                'is_working' => $this->getAction()->isWorking(),
                'status_msg' => $this->getAction()->statusMsg(),
            ],
            'success' => 1,
            'message' => $this->getMesage()
        ];
    }
}
