<?php

namespace App\Http\Controllers\Api\v1\Services\Price;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectationPriceController extends Controller
{
    public function set(Request $request)
    {
        $data = $request->all();
        $complectation = Complectation::find($data['id']);
        $complectation->price = $data['price'];
        $complectation->save();
        return response()->json([
            'data' => $complectation->price
        ]);
    }

    public function get(Complectation $complectation)
    {
        $price = $complectation->price;
        return response()->json([
            'data' => $price
        ]);
    }
}
