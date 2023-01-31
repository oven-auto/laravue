<?php

namespace App\Http\Resources\Trafic;

use Illuminate\Http\Resources\Json\JsonResource;

class TraficSaveResource extends JsonResource
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
            'data' => [
                'id' => $this->id,
                'created_at' => $this->created_at->format('d.m.Y h:i'),
                'updated_at' => $this->updated_at->format('d.m.Y h:i'),
                'time' => $this->created_at->format('d.m.Y h:i'),
                'author_id' => [
                    'id' => $this->author->id,
                    'name' => $this->author->cut_name,
                ],
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'fathername' => $this->fathername,
                'phone' => $this->phone,
                'email' => $this->email,
                'comment' => $this->comment,
                'trafic_sex_id' => [
                    'id' => $this->trafic_sex_id,
                    'name' => $this->sex->name,
                ],
                'trafic_zone_id' => [
                    'id' => $this->trafic_zone_id,
                    'name' => $this->zone->name,
                ],
                'trafic_chanel_id' => [
                    'id' => $this->trafic_chanel_id,
                    'name' => $this->chanel->name,
                ],
                'trafic_brand_id' => [
                    'id' => $this->salon->id,
                    'name' => $this->salon->name,
                ],
                'trafic_section_id' => [
                    'id' => $this->structure->id,
                    'name' => $this->structure->name,
                ],
                'trafic_appeal_id' => [
                    'id' => $this->trafic_appeal_id,
                    'name' => $this->appeal->name,
                ],
                'trafic_need_id' => $this->needs->map(function($item, $key) {
                    return [
                        'id'=>$item->number,
                        'name' => $item->name
                    ];
                }),
                'trafic_action_id' => [
                    'id' => $this->task->id,
                    'name' => $this->task->name,
                ],
                'trafic_interval' => $this->interval,
                'begin_at' => $this->begin_at->format('d.m.Y h:i'),
                'end_at' => $this->end_at->format('d.m.Y h:i'),
                'manager_id' => [
                    'id' => $this->manager->id,
                    'name' => $this->manager->cut_name
                ],
                'status' => $this->trafic_status_id,
                'processing' => [
                    'record' => asset('storage/'.$this->processing->record) . '?' . date('dmyhm'),
                    'audit' => asset('storage/'.$this->processing->audit) . '?' . date('dmyhm'),
                    'result' => $this->processing->result,
                ],
            ],
            'success' => $this->id ? 1 : 0,
        ];
    }
}


