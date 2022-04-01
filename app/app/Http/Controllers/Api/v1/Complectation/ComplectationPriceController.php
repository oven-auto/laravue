<?php

namespace App\Http\Controllers\Api\v1\Complectation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectationPriceController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        $complectation = Complectation::find($data['id']);
        $complectation->price = $data['price'];
        $complectation->save();
        return response()->json([
            'data' => $complectation->price
        ]);
    }
}
