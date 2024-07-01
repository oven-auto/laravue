<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\BodyWork;
use App\Models\VehicleType;

class BodyWorkSelectController extends Controller
{
    public function index()
    {     
        $result = \Cache::remember('list:bodywork', config('cache', 'period'), function() {
            return BodyWork::select('name', 'id')->get();
        });
        
        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }



    public function vehicletypes()
    { 
        $result = \Cache::remember('list:vehicletype', config('cache', 'period'), function() {
            return VehicleType::select('name','id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }
}
