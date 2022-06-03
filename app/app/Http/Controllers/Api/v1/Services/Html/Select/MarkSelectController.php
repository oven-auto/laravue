<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;

class MarkSelectController extends Controller
{
    public function index(Request $request)
    {
        $query = Mark::select(['id','name','prefix']);
        if($request->has('brand_id'))
            $query->where('brand_id', $request->get('brand_id'));
        if($request->has('actual'))
            $query->where('status', 1);
        if($request->has('nonactual'))
            $query->where('status', 0);
        $marks = $query->orderBy('status','DESC')->orderBy('sort')->get();
        return response()->json([
            'status' => 1,
            'data' => $marks,
            'count' => $marks->count()
        ]);
    }
}
