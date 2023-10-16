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
        $canIChange = (
            auth()->user()->id == $this->manager_id
            || auth()->user()->id == $this->author_id
            || auth()->user()->role->permissions->contains('slug', 'trafic_show_alien')
            || (auth()->user()->role->permissions->contains('slug', 'show_trafic_without_manager') && $this->manager_id == '')
        ) ? 1 : 0;

        $showSuperBykovSecurity = ($request->has('author_id') || $request->has('manager_id'));

        return [
            'id' => $this->id,
            'created_at' => $this->created_at->format('d.m.Y (H:i)'),
            'updated_at' => $this->updated_at->format('d.m.Y (H:i)'),
            'author' => $this->author->cut_name,
            'phone' => $showSuperBykovSecurity ? $this->formated_phone : $this->phone_mask,
            'email' => $this->email,
            'comment' => $this->comment,
            'sex' => $showSuperBykovSecurity ? $this->client_name : ($this->person->name ? $this->person->name : $this->sex->name),
            'zone' => $this->zone->name,

            'chanel' => $this->chanel->name,
            'parent_chanel' => $this->chanel->myparent->name,

            'salon' => $this->salon->name,
            'structure' => isset($this->structure) ? $this->structure->name : '',
            'appeal' => isset($this->appeal) ? $this->appeal->name : '',
            'begin_at' => $this->begin_at ? $this->begin_at->format('d.m.Y H:i') : '',
            'end_at' => $this->end_at ? $this->end_at->format('d.m.Y H:i') : '',
            'manager' =>  $this->manager->cut_name,
            'needs' => $this->needs->pluck('name'),
            'client' => ['id'=>$this->worksheet->client_id],
            'worksheet' => [
                'id' => $this->worksheet->id,
                'created_at' => $this->worksheet->created_at ? $this->worksheet->created_at->format('d.m.Y H:i') : null,
            ],
            'interval' => $this->interval,
            'status' => $this->status,
            'processing_at' => $this->processing_at ? $this->processing_at->format('d.m.Y H:i') : ($this->deleted_at ? $this->deleted_at->format('d.m.Y H:i') : ''),
            'processing' => $this->processing->count() ? true : false,
            'files' => $this->files->count() ? true : false,
            'can_i_change' => $canIChange,
            'person' => $this->person->name,
            'inn' => $this->inn,
            'company_name' => $this->company_name,
            'links' =>  $this->links_count,
        ];
    }
}
