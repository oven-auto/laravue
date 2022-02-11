<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Credit\CreditListService;
use App\Http\Resources\Credit\CreditCollection;

class CreditController extends Controller
{	
    public function get(Request $request, CreditListService $service)
    {
    	$data = $request->all();

    	$credits = $service->setData($data)
    		->onCurrentDate()
    		->onActive()
    		->getAll();

    	return new CreditCollection($credits);
    }
}
