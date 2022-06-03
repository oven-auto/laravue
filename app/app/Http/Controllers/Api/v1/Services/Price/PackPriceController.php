<?php

namespace App\Http\Controllers\Api\v1\Services\Price;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;
use App\Services\PriceService\PriceChangeService;

class PackPriceController extends Controller
{
    public function index(Request $request, PriceChangeService $service)
    {
        $service->changePrice(new Pack, $request->all());
        return response()->json([
            'status' => 1,
        ]);
    }
}
