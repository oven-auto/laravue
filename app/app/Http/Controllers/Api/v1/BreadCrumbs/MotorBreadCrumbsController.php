<?php

namespace App\Http\Controllers\Api\v1\BreadCrumbs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\MotorTransmission;
use App\Models\MotorDriver;
use App\Models\MotorType;
use App\Models\MotorToxic;

class MotorBreadCrumbsController extends Controller
{
    public function title(Request $request)
    {
        $res = [];
        if($request->has('brand_id'))
            $res['brand_id'] = Brand::where('id', $request->get('brand_id'))->pluck('name')->implode('');

        if($request->has('name'))
            $res['name'] = $request->get('name');

        if($request->has('motor_transmission_id'))
            $res['motor_transmission_id'] = MotorTransmission::where('id', $request->get('motor_transmission_id'))->first()->name;

        if($request->has('motor_driver_id'))
            $res['motor_driver_id'] = MotorDriver::where('id', $request->get('motor_driver_id'))->first()->name;

        if($request->has('motor_type_id'))
            $res['motor_type_id'] = MotorType::where('id', $request->get('motor_type_id'))->first()->name;

        if($request->has('motor_toxic_id'))
            $res['motor_toxic_id'] = MotorToxic::where('id', $request->get('motor_toxic_id'))->first()->name;

        return response()->json([
            'data' => $res
        ]);
    }
}
