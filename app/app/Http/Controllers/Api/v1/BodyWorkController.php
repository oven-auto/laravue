<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BodyWork;

class BodyWorkController extends Controller
{
    public function index()
    {
        $bodyworks = BodyWork::get();
        if($bodyworks->count())
            return response()->json([
                'status' => 1,
                'data' => $bodyworks,
                'count' => $bodyworks->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного тип кузова'
        ]);
    }

    public function edit(BodyWork $bodywork)
    {
        return response()->json([
            'status' => 1,
            'bodywork' => $bodywork
        ]);
    }

    public function store(BodyWork $bodywork, Request $request)
    {
        $bodywork->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'bodywork' => $bodywork,
            'message' => 'Тип кузова создан'
        ]);
    }

    public function update(BodyWork $bodywork, Request $request)
    {
        $bodywork->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'bodywork' => $bodywork,
            'message' => 'Тип кузова изменен'
        ]);
    }
}
