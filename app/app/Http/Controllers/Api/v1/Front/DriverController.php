<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverType;

class DriverController extends Controller
{
    public function getTypes()
	{
	    $drivers = DriverType::get();
	    return response()->json([
	    	'data' => $drivers,
	    	'status' => $drivers->count() ? 1 : 0
	    ]);
	}
}
