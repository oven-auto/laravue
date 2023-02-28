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
                'created_at' => $this->created_at->format('d.m.Y H:i'),
                'updated_at' => $this->updated_at->format('d.m.Y H:i'),
                'time' => $this->created_at->format('d.m.Y H:i'),
                'author_id' => [
                    'id' => $this->author->id,
                    'name' => $this->author->lastname.' '.$this->author->name
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
                    'id' => $this->company_structure_id,
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

                'trafic_interval' => $this->interval,
                'begin_at' => $this->begin_at ? $this->begin_at->format('d.m.Y H:i') : '',
                'end_at' => $this->end_at->format('d.m.Y H:i'),
                'manager_id' => [
                    'id' => $this->manager->id,
                    'name' => $this->manager->lastname.' '.$this->manager->name
                ],
                'status' => $this->trafic_status_id,
                'processing' => $this->processing->map(function($item){
                    return [
                        'id' => $item->id,
                        'scenario' => $item->standart->name,
                        'user' => $item->user->cut_name,
                        //'record' => $item->getFile('record'),
                        //'audit' => $item->getFile('audit'),
                        'result' => $item->procent,
                        'created_at' => !empty($item->created_at) ? $item->created_at->format('d.m.Y (H:i)') : '',
                        'status' => $item->status_result,
                    ];
                }),
                'files' => $this->files->map(function($item) {
                    return [
                        'id' => $this->id,
                        'name' => $item->name,
                        'file' => $item->getFile('filepath'),
                        'user' => $item->user->cut_name,
                        'created_at' => !empty($item->created_at) ? $item->created_at->format('d.m.Y (H:i)') : '',
                    ];
                }),
                'processing_at' => $this->processing_at ? $this->processing_at->format('d.m.Y (H:i)') : '',
            ],
            'success' => $this->id ? 1 : 0,
        ];
    }
}


