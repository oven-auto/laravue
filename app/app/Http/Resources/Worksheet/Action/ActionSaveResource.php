<?php

namespace App\Http\Resources\Worksheet\Action;

use Illuminate\Http\Resources\Json\JsonResource;

class ActionSaveResource extends JsonResource
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
                'worksheet_status' => $this->getTaskName(),
                'worksheet_id' => $this->getAction()->worksheet_id,
                'action_id' => $this->getAction()->id,
                'begin_at' => $this->getAction()->begin_date,
                'end_at' => $this->getAction()->end_date,
                'task' => $this->getAction()->task->name,
                'is_working' => $this->getAction()->isWorking(),
                'is_waiting' => $this->getAction()->isWaiting(),
                'status_msg' => $this->getAction()->statusMsg(),
                'last_comment' => [
                    'created_at' => $this->getAction()->last_comment->created_at ? $this->getAction()->last_comment->created_at->format('d.m.Y (H:i)') : '',
                    'text' => $this->getAction()->last_comment->text,
                    'author' => $this->getAction()->last_comment->author->cut_name
                ],
            ],
            //'task' => $task,
            'success' => 1,
            'message' => 'Действие создано'
        ];
    }
}
