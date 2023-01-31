<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TraficAppeal;
use DB;
use App\Models\TraficProduct;

class TraficNeedController extends Controller
{
    public function index($trafic_appeal_id)
    {
        $traficAppeal = TraficAppeal::with(['company.brands','structure','appeal'])->findOrFail($trafic_appeal_id);

        $data = TraficProduct::select('number','name')
            ->where('appeal_id', $traficAppeal->appeal_id)
            ->where('company_id', $traficAppeal->company->id)
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
