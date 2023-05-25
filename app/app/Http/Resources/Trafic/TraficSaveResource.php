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
                'time' => $this->created_at->format('d.m.Y (H:i)'),
                'author_id' => $this->author->id,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'fathername' => $this->fathername,
                'phone' => $this->phone,
                'email' => $this->email,
                'comment' => $this->comment,
                'trafic_sex_id'     => $this->trafic_sex_id         ?? '',
                'trafic_zone_id'    => $this->trafic_zone_id        ?? '',
                'trafic_chanel_id'  => $this->trafic_chanel_id      ?? '',
                'trafic_brand_id'   => $this->salon->id             ?? '',
                'trafic_section_id' => $this->company_structure_id  ?? '',
                'trafic_appeal_id'  => $this->trafic_appeal_id      ?? '',
                'trafic_need_id'    => $this->needs->map(function($item, $key) {
                    return $item->number;
                })->toArray(),

                'trafic_interval' => $this->interval,
                'begin_at' => $this->begin_at ? $this->begin_at->format('d.m.Y H:i') : '',
                'end_at' => $this->end_at ? $this->end_at->format('d.m.Y H:i') : '',
                'manager_id' => $this->manager->id,
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
                        'id' => $item->id,
                        'name' => $item->name,
                        'file' => $item->getFile('filepath'),
                        'user' => $item->user->cut_name,
                        'created_at' => !empty($item->created_at) ? $item->created_at->format('d.m.Y (H:i)') : '',
                    ];
                }),
                'processing_at' => $this->processing_at ? $this->processing_at->format('d.m.Y (H:i)') : '',
                'showbuttonstatus' => $this->trafic_status_id == 2 ? 1 : 0,
                'inn' => $this->inn,
                'company_name' => $this->company_name,
                'person_type_id' => $this->client_type_id
            ],
            'success' => $this->id ? 1 : 0,
        ];
    }
}


