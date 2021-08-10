<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceFilter;

class DeviceFilterController extends Controller
{
    public function index()
    {
        $devicefilters = DeviceFilter::get();
        if($devicefilters->count())
            return response()->json([
                'status' => 1,
                'count' => $devicefilters->count(),
                'data' => $devicefilters
            ]);
        return response()->json([
            'status' => 0,
            'count' => $devicefilters->count(),
            'message' => 'Не нашлось ни одного фильтра оборудования'
        ]);
    }

    public function store(DeviceFilter $devicefilter, Request $request)
    {
        $devicefilter->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'filter' => $devicefilter,
            'message' => 'Новый фильтр оборудования создан'
        ]);
    }

    public function edit(DeviceFilter $devicefilter)
    {
        return response()->json([
            'status' => 1,
            'filter' => $devicefilter
        ]);
    }

    public function update(DeviceFilter $devicefilter, Request $request)
    {
        $devicefilter->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'filter' => $devicefilter,
            'message' => 'Фильтр оборудования изменен'
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
