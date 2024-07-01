<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\Tuning;

class TuningController extends Controller
{
    public function devices()
    {
        $result = \Cache::remember('list.devices', config('cache', 'period'), function(){
            return Tuning::select('name','id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }
}
