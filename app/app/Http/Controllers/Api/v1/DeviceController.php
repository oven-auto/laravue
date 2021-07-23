<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{

    public function index(Request $request)
    {
        $query = Device::fullData();

        if($request->has('brand_id'))
            $query->leftJoin('device_brands', 'device_brands.device_id', '=', 'devices.id')
                ->where('device_brands.brand_id', $request->get('brand_id'));

        $devices = $query->orderBy('device_type_id')->orderBy('name')->get();

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
