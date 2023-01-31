<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Http\Resources\Trafic\TraficSexCollection;

class TraficStructureController extends Controller
{
    public function index($brand_id)
    {
        $structures = collect();
        $company = Company::findOrFail($brand_id);
        $data = [];
        if($company)
            foreach($company->structures as $item)
                $data[] = [
                    'id' => $item->pivot->id,
                    'name' => $item->name,
                ];
        return \response()->json([
            'data' => $data,
            'success' => $data ? 1 : 0,
        ]);
    }
}
