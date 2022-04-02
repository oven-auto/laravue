<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceFilter;

class DeviceFilterSelectController extends Controller
{
    public function index()
    {
        $data = DeviceFilter::orderBy('sort')->get();
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
