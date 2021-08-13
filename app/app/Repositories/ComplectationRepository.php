<?php

namespace App\Repositories;

use App\Models\Complectation;

class ComplectationRepository
{
    const COMPLECTATION_COL = [
        'name', 'code', 'sort', 'status', 'brand_id', 'mark_id', 'motor_id', 'price', 'parent_id'
    ];

    public function save(Complectation $complectation, $data= [])
    {
        $this->saveMain($complectation, array_filter($data, function ($key) {
                if (\array_key_exists($key, array_flip(self::COMPLECTATION_COL))) {
                    return true;
                }
            }, ARRAY_FILTER_USE_KEY));
        $this->saveDevices($complectation, $data['devices']);
        $this->savePacks($complectation, $data['packs']);
        $this->saveColors($complectation, $data['colors']);
        //$this->saveColorPacks($complectation, $data['colorPack']);
    }

    public function saveMain(Complectation $complectation, $data = [])
    {
        $complectation->fill($data)->save();
    }

    public function saveDevices(Complectation $complectation, $data = [])
    {
        $complectation->devices()->sync($data);
    }

    public function savePacks($complectation, $data= [])
    {
        $complectation->packs()->sync($data);
    }

    public function saveColors(Complectation $complectation, $data = [])
    {
        $complectation->colors()->detach();
        $complectation->colorPacks()->detach();
        foreach($data as $itemColor) {
            if($itemColor['installColor'])
            {
                $complectation->colors()->attach($itemColor['id']);
                foreach($itemColor['installColorPack'] as $installPackId) {
                    if($installPackId)
                        $complectation->colorPacks()->attach($itemColor['id'], ['pack_id' => $installPackId]);
                }
            }
        }
    }
}
