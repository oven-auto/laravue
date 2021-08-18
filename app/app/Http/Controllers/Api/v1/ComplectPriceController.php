<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectPriceController extends Controller
{
    public function price(Complectation $complectation)
    {
        $price = $complectation->price;
        return response()->json([
            'data' => $price
        ]);
    }
}
