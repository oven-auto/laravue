<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;
use App\Http\Filters\PackFilter;

class PackController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        $query = Pack::fullData()->with('marks');
        $filter = app()->make(PackFilter::class, ['queryParams' => array_filter($data)]);
        $packs = $query->filter($filter)->get();

        if($packs->count())
            return response()->json([
                'status' => 1,
                'data' => $packs,
                'count' => $packs->count(),
                'message' => "Найдено ".$packs->count()." опций"
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного пакета'
        ]);
    }

    public function edit(Pack $pack)
    {
        $pack->devices;
        $pack->marks;

        return response()->json([
            'status' => 1,
            'pack' => $pack
        ]);
    }

    public function store(Pack $pack, Request $request)
    {
        $pack->fill($request->except('devices','marks'))->save();
        $pack->devices()->sync($request->get('devices'));
        $pack->marks()->sync($request->get('marks'));
        return response()->json([
            'status' => 1,
            'pack' => $pack,
            'message' => 'Пакет создан'
        ]);
    }

    public function update(Pack $pack, Request $request)
    {
        $pack->fill($request->except('devices','marks'))->save();
        $pack->devices()->sync($request->get('devices'));
        $pack->marks()->sync($request->get('marks'));
        return response()->json([
            'status' => 1,
            'pack' => $pack,
            'message' => 'Пакет изменен'
        ]);
    }

    public function destroy(Pack $pack)
    {
        $name = $pack->code;
        $pack->delete();
        return response()->json([
            'data' => '',
            'status' => 1,
            'message' => 'Пакет с кодом '.$name.' удален'
        ]);
    }
}
