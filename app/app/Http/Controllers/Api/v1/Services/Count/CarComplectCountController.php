<?php

namespace App\Http\Controllers\Api\v1\Services\Count;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;

class CarComplectCountController extends Controller
{
    public function index(Request $request)
    {
        $count = Car::where('complectation_id', $request->get('complectation_id'))->count();
        return response()->json([
            'data' => $count
        ]);
    }
}
