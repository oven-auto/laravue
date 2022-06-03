<?php

namespace App\Http\Controllers\Api\v1\BreadCrumbs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use App\Models\Brand;

class ColorBreadCrumbsController extends Controller
{
    public function title(Request $request)
    {
        $res = [];

        if($request->has('brand_id'))
            $res['brand_id'] = Brand::where('id', $request->get('brand_id'))->pluck('name')->implode('');

        if($request->has('name'))
            $res['name'] = $request->get('name');

        if($request->has('code'))
            $res['code'] = $request->get('code');

        return response()->json([
            'data' => $res
        ]);
    }
}
