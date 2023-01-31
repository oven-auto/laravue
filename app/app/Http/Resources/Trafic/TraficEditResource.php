<?php

namespace App\Http\Resources\Trafic;

use Illuminate\Http\Resources\Json\JsonResource;

class TraficEditResource extends JsonResource
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
            'created_at' => $this->created_at->format('d.m.Y (h:i)'),
            'updated_at' => $this->updated_at->format('d.m.Y (h:i)'),
            'author' => $this->author->cut_name,
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'fathername' => $this->fathername,
            'phone' => $this->phone_mask,
            'email' => $this->email,
            'comment' => $this->comment,
            'sex' => $this->sex->name,
            'zone' => $this->zone->name,
            'chanel' => $this->chanel->name,
            'salon' => $this->salon->name,
            'structure' => $this->structure->name,
            'appeal' => $this->appeal->name,
            'task' => $this->task->name,
            'begin_at' => $this->begin_at->format('d.m.Y h:i'),
            'end_at' => $this->end_at->format('d.m.Y h:i'),
            'manager' =>  $this->manager->cut_name,
            'needs' => $this->needs->pluck('name'),
            'client' => ['id'=>$this->worksheet->client_id],
            'worksheet' => [
                'id' => $this->worksheet->id,
                'created_at' => $this->worksheet->created_at ? $this->worksheet->created_at->format('d.m.Y h:i') : null,
            ],
            'interval' => $this->interval,
            'status' => $this->status,
            'processing_at' => $this->processing_at ? $this->processing_at->format('d.m.Y h:i') : '',
            'processing' => $this->processing->result ? $this->processing->procent : null,
        ];
    }
}
