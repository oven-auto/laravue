<?php

namespace App\Http\Resources;

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
        $class = null;
        $now = date('y-m-d H:i:s');

        if($this->last_action->end_at )
        {
            if(date('y-m-d') == $this->last_action->end_at->format('y-m-d') && $now < $this->last_action->end_at->format('y-m-d H:i'))
                $class = 'green-bg';
            if($now > $this->last_action->end_at->format('y-m-d H:i:s'))
                $class = 'warning-bg';
        }

        return [
            'id' => $this->id,
            'salon' => $this->company->name,
            'structure' => $this->structure->name,
            'appeal' => $this->appeal->name,
            'executors' => $this->author->cut_name.($this->executors->count() ? ' (+'.$this->executors->count().')' : ''),
            'status' => $this->status->name,
            'client' => $this->client->type->name,
            'action' => $this->last_action->task->name,
            'action_date' => $this->last_action->begin_at ? $this->last_action->begin_at->format('d.m.y (H:i)') : null,
            'check_marker' => [
                'author' => 'Какой-то человек',
                'check_date' => 'Какая-то дата'
            ],
            'row_color' => $class,
            'can_i_change' => 1,
        ];
    }
}
