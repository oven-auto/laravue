<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorToxic;

class MotorToxicController extends Controller
{
    public function index()
    {
        $toxic = MotorToxic::pluck('name', 'id');
        return response()->json([
            'data' => $toxic
        ]);
    }
}
