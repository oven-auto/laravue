<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectationSelectController extends Controller
{
    public function index(Request $request)
    {
        $query = Complectation::with('motor');
        if($request->has('mark_id'))
            $query->where('mark_id', $request->get('mark_id'));
        $data = $query->get();
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
