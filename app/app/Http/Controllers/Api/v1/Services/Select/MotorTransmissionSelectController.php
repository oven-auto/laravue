<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\MotorTransmission;

class MotorTransmissionSelectController extends Controller
{
    public function index()
    {
        $result = \Cache::remember('list:motortransmission', config('cache', 'period'), function() {
            return MotorTransmission::select('name', 'id')->get();
        });
        
        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }
}
