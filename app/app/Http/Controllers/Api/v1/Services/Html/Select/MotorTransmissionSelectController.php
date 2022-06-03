<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorTransmission;

class MotorTransmissionSelectController extends Controller
{
    public function index()
    {
        $data = MotorTransmission::get();
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
