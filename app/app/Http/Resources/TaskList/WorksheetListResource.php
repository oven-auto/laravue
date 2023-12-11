<?php

namespace App\Http\Resources\TaskList;

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
            'id'        => $this->id,
            'type'      => $this->last_action->task->name,
            'status'    => $this->last_action->statusMsg(),
            'client'    => $this->client->fullNameOrType,
            'begin_at'  => $this->last_action->begin_at->format('d.m.Y (H:i)'),
            'end_at'    => $this->last_action->end_at->format('d.m.Y (H:i)'),
            'appeal'    => $this->appeal->name,
            'comment'   => $this->last_action->last_user_comment->text,
            'author'    => $this->author->cut_name,
            'managers'  => $this->executors->map(function($executor){
                return $executor->cut_name;
            }),
            'worksheet_status' => $this->status->name,
            'salon' => $this->company->name,
            'structure' => isset($this->structure) ? $this->structure->name : '',
        ];
    }
}
