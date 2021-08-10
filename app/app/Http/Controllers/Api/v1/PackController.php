<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;

class PackController extends Controller
{
    public function index()
    {
        $packs = Pack::fullData()->get();
        if($packs->count())
            return response()->json([
                'status' => 1,
                'data' => $packs,
                'count' => $packs->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного пакета'
        ]);
    }

    public function edit(Pack $pack)
    {
        $pack->devices;

        return response()->json([
            'status' => 1,
            'pack' => $pack
        ]);
    }

    public function store(Pack $pack, Request $request)
    {
        $pack->fill($request->except('devices'))->save();
        $pack->devices()->sync($request->get('devices'));
        return response()->json([
            'status' => 1,
            'pack' => $pack,
            'message' => 'Пакет создан'
        ]);
    }

    public function update(Pack $pack, Request $request)
    {
        $pack->fill($request->except('devices'))->save();
        $pack->devices()->sync($request->get('devices'));
        return response()->json([
            'status' => 1,
            'pack' => $pack,
            'message' => 'Пакет изменен'
        ]);
    }
}
