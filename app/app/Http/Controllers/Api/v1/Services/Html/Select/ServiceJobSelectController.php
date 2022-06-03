<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceJob;

class ServiceJobSelectController extends Controller
{
    public function index() 
    {
    	$data = ServiceJob::get();
    	return response()->json([
    		'data' => $data,
    		'status' => $data->count() ? 1 : 0
    	]);
    }
}
