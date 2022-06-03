<?php

namespace App\Http\Controllers\Api\v1\Services\Price;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;
use App\Services\PriceService\PriceChangeService;

class ComplectationPriceController extends Controller
{
    public function set(Request $request, PriceChangeService $service)
    {
        $service->changePrice(new Complectation, $request->all());
        return response()->json([
            'status' => 1
        ]);
    }

    public function get(Complectation $complectation)
    {
        $price = $complectation->price;
        return response()->json([
            'data' => $price
        ]);
    }

    public function pricestatus(Request $request)
    {
        $complectation = Complectation::find($request->get('id'));
        $complectation->price_status = $request->get('price_status');
        $complectation->save();
        return response()->json([
            'data' => $complectation->price_status
        ]);
    }
}
