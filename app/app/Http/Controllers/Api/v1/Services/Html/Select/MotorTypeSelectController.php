<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorType;

class MotorTypeSelectController extends Controller
{
    public function index()
    {
        $data = MotorType::pluck('name','id');
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
