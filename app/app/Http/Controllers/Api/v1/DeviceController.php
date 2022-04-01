<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use DB;
class DeviceController extends Controller
{

    public function index(Request $request)
    {
        $query = Device::select(['devices.*','device_types.sort'])->fullData();
        $query->leftJoin('device_types', 'device_types.id', 'devices.device_type_id');

        if($request->has('brand_id'))
            $query->leftJoin('device_brands', 'device_brands.device_id', '=', 'devices.id')
                ->where('device_brands.brand_id', $request->get('brand_id'));

        if($request->has('dops'))
            $query->where('devices.device_type_id', 6);

        if($request->has('name'))
            $query->where('devices.name', 'like', '%' . $request->get('name') . '%');

        if($request->has('device_type_id'))
            $query->where('devices.device_type_id', $request->get('device_type_id'));

        if($request->has('device_filter_id'))
            $query->where('devices.device_filter_id', $request->get('device_filter_id'));

        $devices = $query->orderBy('device_types.sort')->orderBy('devices.name')->get();

        if($request->has('group') && $request->get('group') == 'type')
            $devices = $devices->groupBy('sort');

        if($devices->count())
            return response()->json([
                'status' => 1,
                'data' => $devices,
                'count' => $devices->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного оборудования'
        ]);
    }

    public function store(Device $device, Request $request)
    {
        $device->fill($request->except('brand_id'))->save();
        $device->brands()->sync($request->get('brand_id'));
        return response()->json([
            'status' => 1,
            'device' => $device,
            'message' => 'Новое оборудование создан'
        ]);
    }

    public function edit(Device $device)
    {
        $device->brands;
        return response()->json([
            'status' => 1,
            'device' => $device
        ]);
    }

    public function update(Device $device, Request $request)
    {
        $device->fill($request->except('brand_id'))->save();
        $device->brands()->sync($request->get('brand_id'));
        return response()->json([
            'status' => 1,
            'device' => $device,
            'message' => 'Оборудование изменено'
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
