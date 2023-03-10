<?php

namespace App\Repositories\Trafic;

use App\Models\TraficAppeal;
use App\Models\Appeal;

Class AppealRepository
{
    public function getAppealByCompanyStructure(Int $id)
    {
        $data = [];

        $appeals = TraficAppeal::select('trafic_appeals.*')->with(['appeal' => function($query) {
                $query->orderBy('sort');
            }])
            ->leftJoin('appeals', 'appeals.id', 'trafic_appeals.appeal_id')
            ->where('trafic_appeals.company_structure_id', $id)
            ->where('appeals.show', 1)
            ->get();

        if($appeals)
            foreach($appeals as $item)
                $data[] = [
                    'id' => $item->id,
                    'name' => $item->appeal->name,
                ];

        return $data;
    }

    public function getAppealWithProductByCompanyId($company_id)
    {
        $structures = \App\Models\CompanyStructure::where('company_id', $company_id)->pluck('id');

        $query = Appeal::select('appeals.*', \DB::raw('trafic_appeals.id as uid'))
            ->with('trafic_products.group')
            ->leftJoin('trafic_appeals', 'trafic_appeals.appeal_id', 'appeals.id')
            ->orderBy('appeals.sort')
            ->groupBy('appeals.id')
            ->groupBy('trafic_appeals.id')
            ->whereIn('trafic_appeals.company_structure_id', $structures);

        $appeals = $query->get();
        return $appeals;


    }
}
