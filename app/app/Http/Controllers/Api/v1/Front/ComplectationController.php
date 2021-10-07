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

    public function show($id) 
    {
    	$complectation = Complectation::with(['devices', 'packs', 'motor'])->find($id);
    	return response()->json([
    		'data' => $complectation,
    		'status' => 1
    	]);
    }
}
