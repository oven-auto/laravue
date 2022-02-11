<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Services\Car\CarSearchService;

class CarController extends Controller
{
    public function get(Request $request, $count = 15, CarSearchService $service)
    {        
    	$cars = $service->prepare($request->all())->paginate();
        
    	foreach($cars as $key => $itemCar) 
    		if(strpos($itemCar->color->image, asset('storage')) === false)
    			$itemCar->color->image = asset('storage/' . $itemCar->color->image . '?' . date('dmyh'));

    	return response()->json([
    		'data' => $cars,
    		'status' => 1
    	]);
    }

    public function show(Request $request)
    {
    	$query = Car::select('cars.*','brands.name as brand','marks.name as mark','complectations.name as complectation','car_prices.full_price')
    		->leftJoin('marks', 'marks.id', '=', 'cars.mark_id')
    		->leftJoin('complectations', 'complectations.id', '=', 'cars.complectation_id')
    		->leftJoin('car_prices', 'car_prices.car_id', '=', 'cars.id')
    		->leftJoin('brands', 'brands.id', '=', 'cars.brand_id');
    	if($request->has('car_id')) 
    		$query->where('cars.id', $request->get('car_id'));
    	$car = $query->first();

    	return response()->json([
    		'data' => $car,
    		'status' => 1
    	]);
    }

    public function image(Request $request, $car = [], $color = [])
    {
    	$query = Car::with('color');
    	if($request->has('car_id')) {
    		$query->where('id', $request->get('car_id'));
    		$car = $query->first();
    		$car->color->image = asset('storage/' . $car->color->image . '?' . date('dmyh'));
    		$color = $car->color;
    	}

    	return response()->json([
    		'data' => $color,
    		'status' => $color->count() ? 1 : 0
    	]);
    }

    public function count(Request $request) 
    {
    	$count = Car::count();
    	return response()->json([
    		'count' => $count
    	]);
    }
}
