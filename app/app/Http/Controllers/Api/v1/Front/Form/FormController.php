<?php

namespace App\Http\Controllers\Api\v1\Front\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;

class FormController extends Controller
{
    public function get(Request $request)
    {
    	$query = Form::select('forms.*')->with('bodies');
    	if($request->has('id'))
    		$query->where('forms.id',$request->get('id'));
        if($request->has('brand_id'))
            $query->where('forms.brand_id', $request->get('brand_id'));
        if($request->has('form_event'))
            $query->leftJoin('form_events', 'form_events.id', 'forms.form_event_id')
                ->where('form_events.name', $request->get('form_event'));
    	
    	$data = $query->first();
    	//$data->body = json_decode($data->body);
        //dd($data);
    	return response()->json([
    		'data' => $data,
    		'status' => $data ? 1 : 0
    	]);
    }
}
