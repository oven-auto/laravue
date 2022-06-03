<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceType;

class DeviceTypeSelectController extends Controller
{
    public function index()
    {
        $data = DeviceType::orderBy('sort')->get();
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
