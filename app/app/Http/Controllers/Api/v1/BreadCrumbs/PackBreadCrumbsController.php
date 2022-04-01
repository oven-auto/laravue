<?php

namespace App\Http\Controllers\Api\v1\BreadCrumbs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Mark;

class PackBreadCrumbsController extends Controller
{
    public function title(Request $request)
    {
        $res = [];
        if($request->has('brand_id'))
            $res['brand_id'] = Brand::where('id', $request->get('brand_id'))->pluck('name')->implode('');
        if($request->has('mark_id'))
            $res['mark_id'] = Mark::where('id', $request->get('mark_id'))->pluck('name')->implode('');
        if($request->has('code'))
            $res['code'] = $request->get('code');
        return response()->json([
            'data' => $res
        ]);
    }
}
