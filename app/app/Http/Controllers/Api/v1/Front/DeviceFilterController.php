<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceFilter;

class DeviceFilterController extends Controller
{
    public function get() 
    {	
    	$filters = DeviceFilter::get();

    	return response()->json([
    		'status' => $filters->count() ? 1 : 0,
    		'data' => $filters
    	]);
    }
}
