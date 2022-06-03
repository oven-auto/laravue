<?php

namespace App\Http\Controllers\Api\v1\Services\Count;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;

class DeviceFilterCountController extends Controller
{
    public function index(Request $request)
    {
        $count = Device::where('device_filter_id', $request->get('device_filter_id'))->count();
        return response()->json([
            'data' => $count,
            'status' => 1
        ]);
    }
}
