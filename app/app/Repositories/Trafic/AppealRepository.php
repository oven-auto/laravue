<?php

namespace App\Repositories\Trafic;

use App\Models\TraficAppeal;
use App\Models\Appeal;

Class AppealRepository
{
    public function getAppealByCompanyStructure(Int $id)
    {
        $data = [];

        $appeals = TraficAppeal::with('appeal')
            ->where('company_structure_id', $id)
            ->get();

        if($appeals)
            foreach($appeals as $item)
                $data[] = [
                    'id' => $item->id,
                    'name' => $item->appeal->name,
                ];

        return $data;
    }
}
