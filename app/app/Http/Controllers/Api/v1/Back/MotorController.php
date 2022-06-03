<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motor;

class MotorController extends Controller
{
    public function index(Request $request)
    {
        $query = Motor::fullData();

        if($request->has('brand_id'))
            $query->where('brand_id', $request->get('brand_id'));

        $motors = $query->orderBy('brand_id')
            ->orderBy('power')
            ->get();

        if($motors->count())
            return response()->json([
                'status' => 1,
                'data' => $motors,
                'count' => $motors->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного агрегата'
        ]);
    }

    public function edit(Motor $motor)
    {
        return response()->json([
            'status' => 1,
            'motor' => $motor
        ]);
    }

    public function store(Motor $motor, Request $request)
    {
        $motor->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motor' => $motor,
            'message' => 'Спецификация агрегата создана'
        ]);
    }

    public function update(Motor $motor, Request $request)
    {
        $motor->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motor' => $motor,
            'message' => 'Спецификация агрегата изменена'
        ]);
    }
}
