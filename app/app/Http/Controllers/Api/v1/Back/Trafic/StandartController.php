<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditStandart;
use App\Http\Resources\Trafic\TraficSexCollection;

class StandartController extends Controller
{
    public function __invoke()
    {
        $standarts = AuditStandart::select('id','name','target')->where('trafic',1)->get();
        return response()->json([
            'data' => $standarts,
            'success' => 1
        ]);
    }
}
