<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;

class MarkController extends Controller
{
    public function list()
    {
    	$marks = Mark::select('name', 'prefix', 'id', 'body_work_id')->with(['icon','bodywork'])->get();
    	foreach ($marks as $key => $item) {
    		$item->icon->image = asset('storage'.$item->icon->image) . '?' . date('dmyhm');
    	}
    	return response()->json([
    		'data' => $marks,
    		'status' => 1
    	]);
    }
}
