<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;

class FormSelectController extends Controller
{
    public function index(Request $request)
    {
        $query = Form::select();

        if($request->has('widget') && $request->get('widget') == 1)
            $query->where('widget_status', 1);

        $forms = $query->get();

        return response()->json([
            'data' => $forms,
            'status' => 1
        ]);
    }
}
