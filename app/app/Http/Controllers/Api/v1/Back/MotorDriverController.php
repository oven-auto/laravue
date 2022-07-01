<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorDriver;

class MotorDriverController extends Controller
{
    public function index()
    {
        $motordrivers = MotorDriver::get();
        if($motordrivers->count())
            return response()->json([
                'status' => 1,
                'data' => $motordrivers,
                'count' => $motordrivers->count(),
                'message' => 'Найдено '.$motordrivers->count().' едениц типов привода'
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного типа привода'
        ]);
    }

    public function edit(MotorDriver $motordriver)
    {
        return response()->json([
            'status' => 1,
            'motordriver' => $motordriver
        ]);
    }

    public function store(MotorDriver $motordriver, Request $request)
    {
        $motordriver->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motordriver' => $motordriver,
            'message' => 'Привод создан'
        ]);
    }

    public function update(MotorDriver $motordriver, Request $request)
    {
        $motordriver->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'motordriver' => $motordriver,
            'message' => 'Привод изменен'
        ]);
    }


}
