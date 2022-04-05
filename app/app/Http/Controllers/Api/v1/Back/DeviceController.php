<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use DB;
use App\Http\Filters\DeviceFilter;

class DeviceController extends Controller
{

    public function index(Request $request)
    {
        $data = $request->all();
        $query = Device::select(['devices.*','device_types.sort'])
            ->fullData()
            ->leftJoin('device_types', 'device_types.id', 'devices.device_type_id');
        $filter = app()->make(DeviceFilter::class, ['queryParams' => array_filter($data)]);
        $devices = $query->filter($filter)
            ->orderBy('device_types.sort')
            ->orderBy('devices.name')
            ->get();

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
