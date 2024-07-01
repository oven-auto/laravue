<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\MotorType;

class MotorTypeSelectController extends Controller
{
    public function index()
    {
        $result = \Cache::remember('list:motortype', config('cache', 'period'), function() {
            return MotorType::select('name','id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }
}
