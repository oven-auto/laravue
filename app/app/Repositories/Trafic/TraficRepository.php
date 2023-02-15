<?php

namespace App\Repositories\Trafic;

use App\Models\Trafic;
use DB;
use App\Http\Filters\TraficFilter;
use Auth;
use \App\Http\Resources\Trafic\TraficEditCollection;
use \App\Http\Resources\Trafic\TraficSaveResource;

Class TraficRepository
{
    public function save(Trafic $trafic, $data)
    {

            return DB::transaction(function () use ($trafic, $data){

                if(!$trafic->created_at)
                    $trafic->created_at                 = isset($data['time']) ? date('Y-m-d H:i',\strtotime($data['time'])) : '';

                $trafic->author_id                      = isset($data['author_id']) ? $data['author_id'] : '';

                $trafic->firstname                      = isset($data['firstname']) ? $data['firstname'] : '';

                $trafic->lastname                       = isset($data['lastname']) ? $data['lastname'] : '';

                $trafic->fathername                     = isset($data['fathername']) ? $data['fathername'] : '';

                $trafic->phone                          = isset($data['phone']) ?preg_replace("/[^,.0-9]/", '', $data['phone']) : '';

                $trafic->email                          = isset($data['email']) ? $data['email'] : '';

                $trafic->comment                        = isset($data['comment']) ? $data['comment'] : '';

                if(isset($data['trafic_sex_id']))
                    $trafic->trafic_sex_id                  = $data['trafic_sex_id'];

                if(isset($data['trafic_zone_id']))
                    $trafic->trafic_zone_id                 = $data['trafic_zone_id'];

                if(isset($data['trafic_chanel_id']))
                    $trafic->trafic_chanel_id               = $data['trafic_chanel_id'];

                if(isset($data['trafic_brand_id']))
                    $trafic->company_id                     = $data['trafic_brand_id'];

                if(isset($data['trafic_section_id']))
                    $trafic->company_structure_id           = $data['trafic_section_id'];

                if(isset($data['trafic_appeal_id']))
                    $trafic->trafic_appeal_id               = $data['trafic_appeal_id'];

                // if(isset($data['trafic_action_id']))
                //     $trafic->task_id                        = $data['trafic_action_id'];

                if(isset($data['begin_at']))
                    $trafic->begin_at                       = date('Y-m-d H:i',\strtotime($data['begin_at']));

                if(isset($data['end_at']))
                    $trafic->end_at                         = date('Y-m-d H:i',\strtotime($data['end_at']));

                if(isset($data['manager_id']))
                    $trafic->manager_id                     = $data['manager_id'];

                if(isset($data['trafic_interval']))
                    $trafic->interval = $data['trafic_interval'];

                $trafic->save();

                if(isset($data['trafic_need_id'])) {
                    $trafic->saveNeeds()->delete();
                    foreach($data['trafic_need_id'] as $item)
                        $trafic->saveNeeds()->create(['trafic_product_number' => $item['id']]);
                }
                return $trafic;
            });
    }

    private function filter($data = [])
    {
        $query = Trafic::select('trafics.*')->withTrashed();
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);
        return $query
            ->filter($filter)
            ->orderBy(DB::raw('trafics.manager_id IS NULL'),'DESC')
            ->orderBy('trafics.created_at','DESC')
            ->groupBy('trafics.id');
    }

    public function paginate($data = [])
    {
        $query = $this->filter($data);
        $query->with([
            'needs', 'sex', 'zone', 'chanel.myparent',
            'salon', 'structure', 'appeal', 'manager',
            'author', 'worksheet', 'processing', 'files'
        ]);
        $result = $query->simplePaginate(10);
        return $result;
    }

    public function export($data = [])
    {
        $query = $this->filter($data);
        $result = $query->get();
        return $result;
    }

    public function counter($data = [])
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);
        $sub = Trafic::query()->withTrashed();
        $sub->filter($filter)
            ->groupBy('trafics.id')
            ->get();
        $result = DB::query()->fromSub($sub, 'cr')->count();
        return $result;
    }

    public function find($id)
    {
        $result = Trafic::fullest()->find($id);
        return $result;
    }
}
