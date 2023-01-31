<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TraficAppeal;

class TraficAppealController extends Controller
{
    public function index($id)
    {
        $data = [];
        $appeals = TraficAppeal::with('appeal')->where('company_structure_id', $id)->get();
        if($appeals)
            foreach($appeals as $item)
                $data[] = [
                    'id' => $item->id,
                    'name' => $item->appeal->name,
                ];

        return response()->json([
            'data' => $data,
            'success' => $data ? 1  : 0,
        ]);
    }
}

/*CREATE view trafic_products as
SELECT concat('model',m.id) as id, m.name, 1 as appeal_id, cb.company_id
from marks m
left join brands b on m.brand_id = b.id
LEFT JOIN company_brands cb on b.id = cb.brand_id
UNION SELECT concat('service',id) as id, name, appeal_id, company_id from service_products sp */
