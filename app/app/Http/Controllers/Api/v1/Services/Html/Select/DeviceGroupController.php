<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Repositories\Device\DeviceRepository;

class DeviceGroupController extends Controller
{
    public function index(Request $request, DeviceRepository $repo)
    {
        $query = Device::query()
            ->select(['devices.*','device_types.sort'])
            ->fullData()
            ->leftJoin('device_brands', 'device_brands.device_id','=','devices.id')
            ->leftJoin('device_types', 'device_types.id','=','devices.device_type_id');
        if($request->has('brand_id'))
            $query->where('device_brands.brand_id', $request->get('brand_id'));
        $devices = $query
            ->orderBy('device_types.sort')
            ->orderBy('devices.name')
            ->get();
        $devices = $devices->groupBy('sort');

        return response()->json([
            'data' => $devices,
            'status' => 1,
            'count' => $devices->count()
        ]);
    }
}
