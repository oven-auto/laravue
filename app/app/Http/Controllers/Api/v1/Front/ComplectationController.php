<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;
use App\Services\Complectation\ComplectationService;

class ComplectationController extends Controller
{
    private $service;

    public function __construct(ComplectationService $service)
    {
        $this->service = $service;
    }

    public function get(Request $request)
    {
    	$complectations = $this->service->getAll($request->all());

    	return response()->json([
    		'data' => $complectations,
    		'status' => 1
    	]);
    }

    public function show($id, Request $request) 
    {
        $complectation = $this->service->getById($id, $request->all());
    	
        return response()->json([
    		'data' => $complectation,
    		'status' => 1
    	]);
    }

    public function image($id, Request $request)
    {
        $complectation = $this->service->getComplectationImages($id);
        
        return response()->json([
            'data' => $complectation,
            'status' => 1
        ]);
    }
}
