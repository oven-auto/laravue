<?php

namespace App\Http\Resources\Trafic;

use Illuminate\Http\Resources\Json\JsonResource;

class TraficProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $co = $this->trafic_products->max('group_id');
        return [
            'id' => $this->uid,
            'name' => $this->name,
            'childrens' => $this->trafic_products->groupBy(function($mas, $i) use ($co){
                return $mas['group_id'] ?? $co+1;
            })->map(function($itemGroup,$key){
                $arr['title'] = isset($itemGroup[0]->group) ? $itemGroup[0]->group->name : '';
                $arr['elements'] = $itemGroup->map(function($itemProduct){
                    return [
                        'uid' => $itemProduct->uid,
                        'name' => $itemProduct->name,
                        'id' => $itemProduct->number,
                        'description' => $itemProduct->description,
                        'duration' => $itemProduct->duration ? $itemProduct->duration : '',
                        'price' => ($itemProduct->price) ? number_format($itemProduct->price,0,'',' ') : '',
                        'group' => $itemProduct->group
                    ];
                });
                return $arr;
            })
        ];
    }
}
