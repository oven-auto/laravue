<?php

namespace App\Http\Controllers\Api\v1\Front\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Form\FormRepository;

class FormController extends Controller
{
    public function get(Request $request, FormRepository $repository)
    {
        $data = $repository->findBy($request->all());

    	return response()->json([
    		'data' => $data,
    		'status' => $data ? 1 : 0
    	]);
    }
}
