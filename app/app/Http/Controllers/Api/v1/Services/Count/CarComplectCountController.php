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
        $count = Car::select(['car_deliveries.delivery_type_id','delivery_types.name',DB::raw('count(car_deliveries.car_id) as count')])
            ->leftJoin('car_deliveries','car_deliveries.car_id','cars.id')
            ->leftJoin('delivery_types','delivery_types.id', 'car_deliveries.delivery_type_id')
            ->where('cars.complectation_id', $request->get('complectation_id'))
            ->groupBy('car_deliveries.delivery_type_id')
            ->get();
        return response()->json([
            'data' => $count
        ]);
    }
}
