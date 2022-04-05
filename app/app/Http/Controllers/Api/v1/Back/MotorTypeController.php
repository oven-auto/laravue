<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorType;

class MotorTypeController extends Controller
{
    public function index()
    {
        $motortypes = MotorType::get();
        if($motortypes->count())
            return response()->json([
                'status' => 1,
                'data' => $motortypes,
                'count' => $motortypes->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного тип мотора'
        ]);
    }

    public function edit(MotorType $motortype)
    {
        return response()->json([
            'status' => 1,
            'motortype' => $motortype
        ]);
    }

    public function store(MotorType $motortype, Request $request)
    {
        $motortype->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motortype' => $motortype,
            'message' => 'Тип создан'
        ]);
    }

    public function update(MotorType $motortype, Request $request)
    {
        $motortype->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motortype' => $motortype,
            'message' => 'motor изменен'
        ]);
    }
}
