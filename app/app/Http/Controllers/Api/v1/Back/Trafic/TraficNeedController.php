<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TraficAppeal;
use DB;
use App\Models\TraficProduct;
use App\Http\Resources\Trafic\TraficProductCollection;

class TraficNeedController extends Controller
{
    public function index($company_id = 0, \App\Repositories\Trafic\AppealRepository $service)
    {
        $start_memory = memory_get_usage();
        $appeals = $service->getAppealWithProductByCompanyId($company_id);
        $memory = memory_get_usage() - $start_memory;
        return (new TraficProductCollection($appeals))
            ->additional(['memory'=>number_format($memory/1024/1024,2, '.', '').' Mb']);
    }

    public function appealneed($trafic_appeal_id = '')
    {
        //$traficAppeal = TraficAppeal::find($trafic_appeal_id);

        $query = TraficProduct::select('number','name');

        //if($traficAppeal)
            //$query->where('appeal_id', $traficAppeal->appeal_id);

        $data = $query->orderBy('number')
            ->where('appeal_id', '<>', 12)
            ->get();

        $arr = [];
        foreach($data as $item)
            $arr[] = ['id' => $item->number, 'name' => $item->name];

        return response()->json([
            'data' => $arr,
            'success' => 1,
        ]);
    }

    public function models($company_id = '')
    {
        $data = TraficProduct::select('trafic_products.number','trafic_products.name','marks.id')
            ->leftJoin('marks','marks.id','trafic_products.uid')
            ->where('appeal_id', 12)
            ->where('marks.status','>',0)
            ->whereNotNull('marks.id')
            ->orderBy('marks.sort')
            ->get();

        $arr = [];

        foreach($data as $item)
            $arr[] = ['id' => $item->number, 'name' => $item->name];

        return response()->json([
            'data' => $arr,
            'success' => 1,
        ]);
    }
}
