<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceType;

class DeviceTypeController extends Controller
{

    public function index()
    {
        $types = DeviceType::orderBy('sort')->get();
        if($types->count())
            return response()->json([
                'status' => 1,
                'count' => $types->count(),
                'data' => $types
            ]);
        return response()->json([
            'status' => 0,
            'count' => $types->count(),
            'message' => 'Не нашлось ни одного типа оборудования'
        ]);
    }

    public function store(DeviceType $devicetype, Request $request)
    {
        $devicetype->fill($request->input());
        $devicetype->sort = DeviceType::max('sort')+1;
        $devicetype->save();
        return response()->json([
            'status' => 1,
            'type' => $devicetype,
            'message' => 'Новый тип оборудования создан'
        ]);
    }

    public function edit(DeviceType $devicetype)
    {
        return response()->json([
            'status' => 1,
            'type' => $devicetype
        ]);
    }

    public function update(DeviceType $devicetype, Request $request)
    {
        $devicetype->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'type' => $devicetype,
            'message' => 'Тип оборудования изменен'
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
