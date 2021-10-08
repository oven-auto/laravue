<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectationController extends Controller
{
    public function get(Request $request)
    {
    	$query = Complectation::with(['motor']);
    	if($request->has('mark_id'))
    		$query
    			->withCount('cars')
    			->where('mark_id', $request->get('mark_id'));

    	$complectations = $query->get();
    	return response()->json([
    		'data' => $complectations,
    		'status' => 1
    	]);
    }

    public function show($id, Request $request) 
    {
        $query = Complectation::with('motor');
        
        if($request->has('devices'))
            $query->with('devices');
        if($request->has('packs'))
            $query->with('packs');
        if($request->has('brand'))
            $query->with('brand');
        if($request->has('mark'))
            $query->with('mark');

    	$complectation = $query->find($id);
    	
        return response()->json([
    		'data' => $complectation,
    		'status' => 1
    	]);
    }

    public function image($id, Request $request)
    {
        $complectation = Complectation::with(['colors','colorPacks'])->find($id);
        foreach ($complectation->colors as $key => $item) {
            $item->image = asset('storage/' . $item->image . '?' . date('dmyh'));
        }
        return response()->json([
            'data' => $complectation,
            'status' => 1
        ]);
    }
}
