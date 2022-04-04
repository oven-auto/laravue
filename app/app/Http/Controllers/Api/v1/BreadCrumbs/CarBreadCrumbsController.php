<?php

namespace App\Http\Controllers\Api\v1\BreadCrumbs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Mark;
use App\Models\Complectation;

class CarBreadCrumbsController extends Controller
{
    public function title(Request $request)
    {
        $res = [];

        if($request->has('brand_id'))
            $res['brand_id'] = Brand::where('id', $request->get('brand_id'))->pluck('name')->implode('');

        if($request->has('mark_id'))
            $res['mark_id'] = Mark::where('id', $request->get('mark_id'))->pluck('name')->implode('');

        if($request->has('complectation_id'))
            $res['complectation_id'] = Complectation::where('id', $request->get('complectation_id'))->pluck('name')->implode('');

        if($request->has('vin'))
            $res['vin'] = $request->get('vin');

        return response()->json([
            'data' => $res
        ]);
    }
}
