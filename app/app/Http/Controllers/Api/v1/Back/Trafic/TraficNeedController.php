<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TraficAppeal;
use DB;
use App\Models\TraficProduct;
use App\Http\Resources\Trafic\TraficSexCollection;

class TraficNeedController extends Controller
{
    public function index($trafic_appeal_id)
    {
        $traficAppeal = TraficAppeal::with(['company.brands','structure','appeal'])->findOrFail($trafic_appeal_id);

        $appeals = TraficProduct::select('number','name')
            ->where('appeal_id', $traficAppeal->appeal_id)
            ->where('company_id', $traficAppeal->company->id)
            ->orderBy('number')
            ->get();

        $data = $appeals->map(function($item) {
            return (object) ['id' => $item->number, 'name' => $item->name];
        });

        return (new TraficSexCollection($data))->additional([
            'error' => 'Товаров или услуг не найдено'
        ]);
    }
}
