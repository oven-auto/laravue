<?php

namespace App\Http\Controllers\Api\v1\BreadCrumbs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\DeviceFilter;
use App\Models\Brand;

class DeviceBreadCrumbsController extends Controller
{
    public function title(Request $request)
    {
        $res = [];
        if($request->has('brand_id'))
            $res['brand_id'] = Brand::where('id', $request->get('brand_id'))->pluck('name')->implode('');

        if($request->has('device_type_id'))
            $res['device_type_id'] = DeviceType::where('id', $request->get('device_type_id'))->pluck('name')->implode('');

        if($request->has('device_filter_id'))
            $res['device_filter_id'] = DeviceFilter::where('id', $request->get('device_filter_id'))->pluck('name')->implode('');

        if($request->has('name'))
            $res['name'] = $request->get('name');

        if($request->has('tuning'))
            if($request->get('tuning') == 1)
                $res['tuning'] = 'Только тюнинг';
            elseif($request->get('tuning')==2)
                $res['tuning'] = 'Все кроме тюнинга';

        if($request->has('install_target'))
            if($request->get('install_target') == 1)
                $res['install_target'] = 'Только с ЦКУ';
            elseif($request->get('install_target')==2)
                $res['install_target'] = 'Все кроме ЦКУ';

        return response()->json([
            'data' => $res
        ]);
    }
}
