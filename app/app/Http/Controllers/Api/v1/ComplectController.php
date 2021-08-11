<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectController extends Controller
{
    public function index()
    {
        $complectations = Complectation::get();
        if($complectations->count())
            return response()->json([
                'status' => 1,
                'data' => $complectations,
                'count' => $complectations->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одной комплектации'
        ]);
    }

    public function edit(Complectation $complectation)
    {

    }

    public function store(Complectation $complectation, Request $request)
    {
        dd($request->all());
    }

    public function update(Complectation $complectation, Request $request)
    {

    }
}
