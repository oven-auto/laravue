<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransmissionType;

class TransmissionController extends Controller
{
	public function getTypes()
	{
	    $transmissions = TransmissionType::get();
	    return response()->json([
	    	'data' => $transmissions,
	    	'status' => $transmissions->count() ? 1 : 0
	    ]);
	}
}
