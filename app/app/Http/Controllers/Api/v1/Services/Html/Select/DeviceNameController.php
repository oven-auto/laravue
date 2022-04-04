<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;

class DeviceNameController extends Controller
{
    public function index(Request $request)
    {
        $ids = explode(',',$request->get('ids'));
        $nameDevices = Device::select(['id','name'])
            ->whereIn('id', $ids)
            ->orderBy('device_type_id')
            ->orderBy('name')
            ->pluck('name','id');
        return response()->json([
            'status'=>1,
            'data'=>$nameDevices
        ]);
    }
}
