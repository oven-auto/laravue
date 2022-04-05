<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorTransmission;

class MotorTransmissionController extends Controller
{
    public function index()
    {
        $motortransmissions = MotorTransmission::get();
        if($motortransmissions->count())
            return response()->json([
                'status' => 1,
                'data' => $motortransmissions,
                'count' => $motortransmissions->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного тип трансмиссий'
        ]);
    }

    public function edit(MotorTransmission $motortransmission)
    {
        return response()->json([
            'status' => 1,
            'motortransmission' => $motortransmission
        ]);
    }

    public function store(MotorTransmission $motortransmission, Request $request)
    {
        $motortransmission->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motortransmission' => $motortransmission,
            'message' => 'Трансмиссия создана'
        ]);
    }

    public function update(MotorTransmission $motortransmission, Request $request)
    {
        $motortransmission->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motortransmission' => $motortransmission,
            'message' => 'Трансмиссия изменена'
        ]);
    }
}
