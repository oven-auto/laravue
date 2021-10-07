<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Credit;
use App\Repositories\CreditRepository;

class CreditController extends Controller
{
    public function get(Request $request, CreditRepository $service, $credits = false)
    {
    	if($request->has('mark_id'))
    		$credits = $service->getCreditsByMarkId($request->get('mark_id'));

    	return response()->json([
    		'data' => $credits,
    		'status' => ($credits) ? 1 : 0
    	]);
    }
}
