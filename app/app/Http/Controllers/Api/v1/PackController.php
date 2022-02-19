<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;

class PackController extends Controller
{
    public function index(Request $request)
    {
        $query = Pack::fullData()->with('marks');

        if($request->has('brand_id'))
            $query->where('brand_id', $request->get('brand_id'));

        if($request->has('complectation_id'))
            $query->rightJoin('complectation_packs', 'complectation_packs.pack_id', '=', 'packs.id')
                ->where('complectation_packs.complectation_id', $request->get('complectation_id'));

        if($request->has('mark_id'))
            $query->leftJoin('pack_marks', 'pack_marks.pack_id', '=', 'packs.id')
                ->where('pack_marks.mark_id', $request->get('mark_id'));

        if($request->has('code'))
            $query->where('packs.code', 'like', '%'.$request->get('code').'%');

        $packs = $query->get();

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
}
