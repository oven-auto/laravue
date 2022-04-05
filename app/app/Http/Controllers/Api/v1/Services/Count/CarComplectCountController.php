<?php

namespace App\Http\Controllers\Api\v1\Services\Count;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use DB;

class CarComplectCountController extends Controller
{
    public function index(Request $request)
    {
        $count = Car::select(['cars.delivery_type_id','delivery_types.name',DB::raw('count(cars.id) as count')])
            ->leftJoin('delivery_types','delivery_types.id', 'cars.delivery_type_id')
            ->where('complectation_id', $request->get('complectation_id'))
            ->groupBy('cars.delivery_type_id')
            ->get();
        return response()->json([
            'data' => $count
        ]);
    }
}
