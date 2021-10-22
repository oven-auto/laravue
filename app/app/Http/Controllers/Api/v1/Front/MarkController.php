<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;

class MarkController extends Controller
{
    public function list()
    {
    	$marks = Mark::select('name', 'prefix', 'id', 'body_work_id','slug')->with(['icon','bodywork','basecomplectation'])->get();
    	foreach ($marks as $key => $item) {
    		$item->icon->image = asset('storage'.$item->icon->image) . '?' . date('dmyhm');
    	}
    	return response()->json([
    		'data' => $marks,
    		'status' => $marks->count() ? 1 : 0,
    	]);
    }

    public function get($slug)
    {
    	$mark = Mark::with(['bodywork','banner', 'brand', 'info', 'properties','markcolors'])->where('slug',$slug)->first();
    	$mark->banner->image = asset('storage'.$mark->banner->image) . '?' . date('dmyhm');
    	foreach($mark->markcolors as $itemColor) {
    		$itemColor->image = asset('storage'.$itemColor->image . '?' . date('dmyh'));
    	}
    	return response()->json([
    		'data' => $mark,
    		'status' => $mark->count() ? 1 : 0
    	]);
    }

    public function getMarksName()
    {
        $marks = Mark::select('name','prefix','id')->get();
        return response()->json([
            'data' => $marks,
            'status' => $marks->count() ? 1 : 0
        ]);
    }
}
