<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motor;

class MotorController extends Controller
{
    public function index()
    {
        $motors = Motor::fullData()->get();
        if($motors->count())
            return response()->json([
                'status' => 1,
                'data' => $motors,
                'count' => $motors->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного мотора'
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
            'message' => 'Мотор создан'
        ]);
    }

    public function update(Motor $motor, Request $request)
    {
        $motor->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motor' => $motor,
            'message' => 'Мотор изменен'
        ]);
    }
}
