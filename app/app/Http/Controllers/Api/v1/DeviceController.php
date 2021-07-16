<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{

    public function index()
    {
        $devices = Device::fullData()->get();
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
        $device->brands()->attach($request->get('brand_id'));
        return response()->json([
            'status' => 1,
            'device' => $device,
            'message' => 'Новое оборудование создан'
        ]);
    }

    public function edit(Device $device)
    {
        return response()->json([
            'status' => 1,
            'device' => $device
        ]);
    }

    public function update(Device $device, Request $request)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
