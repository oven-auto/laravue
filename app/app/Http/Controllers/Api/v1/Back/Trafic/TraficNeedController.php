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

    public function appealneed($trafic_appeal_id)
    {
        $traficAppeal = TraficAppeal::with(['company.brands','structure','appeal'])->findOrFail($trafic_appeal_id);

        $data = TraficProduct::select('number','name')
            ->where('appeal_id', $traficAppeal->appeal_id)
            ->orderBy('number')
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
