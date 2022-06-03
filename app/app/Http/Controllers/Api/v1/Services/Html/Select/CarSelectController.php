<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;

class CarSelectController extends Controller
{
    public function index(Request $request)
    {
        $cars = Car::select('cars.*')
        	->with(['mark','complectation'])
        	->leftJoin('car_deliveries','car_deliveries.car_id','cars.id')
        	->where('car_deliveries.delivery_type_id',$request->get('delivery_type'))
        	->get();

        return response()->json([
        	'data' => $cars,
        	'status' => $cars->count()?1:0
        ]);
    }
}
