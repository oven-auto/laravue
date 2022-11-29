<?php

namespace App\Repositories\Complectation;

use App\Models\Complectation;
use DB;
use App\Http\Filters\ComplectationFilter;
use Illuminate\Support\Arr;

class ComplectationRepository
{
    public function filter($data = [])
    {
        $query = Complectation::with(['motor','brand','mark','lastmoderator.user']);
        $filter = app()->make(ComplectationFilter::class, ['queryParams' => array_filter($data)]);
        $complectations = $query->filter($filter)
            ->orderBy('mark_id')
            ->orderBy('sort')
            ->get();
        return $complectations;
    }

    public function save(Complectation $complectation, $data= [])
    {
        try {
            DB::transaction(function () use ($complectation, $data) {

                $complectation->fill(Arr::except($data, ['devices','packs','install_colors','color_pack']))->save();

                isset($data['devices']) ? $complectation->devices()->sync($data['devices']) : $complectation->devices()->detach();
                isset($data['packs']) ? $complectation->packs()->sync($data['packs']) : $complectation->packs()->detach();
                isset($data['install_colors']) ? $complectation->colors()->sync($data['install_colors']) : $complectation->colors()->detach();

                $complectation->colorPacks()->detach();
                if(isset($data['color_pack']) && \is_array($data['color_pack']))
                    foreach($data['color_pack'] as $colorId => $packArrIds)
                        foreach($packArrIds as $itemPackId)
                            $complectation->colorPacks()->attach($colorId, ['pack_id' => $itemPackId]);
            });
        } catch(\Exception $e) {
			die($e);
		}
    }
}
