<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\Marker;
use App\Models\TradeMarker;

class MarkerSelectController extends Controller
{
    public function index()
    {
        $result = \Cache::remember('list:marker', config('cache', 'period'), function(){
            return Marker::select('name', 'id')->get();
        });
        
        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }



    public function trademarker()
    {
        $result = \Cache::remember('list:trademarker', config('cache', 'period'), function(){
            return TradeMarker::select('name', 'id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }
}
