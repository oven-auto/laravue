<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    public function get(Request $request)
    {
    	$query = Car::with(['brand','complectation', 'mark', 'color', 'price']);
    	if($request->has('complectation_id'))
    		$query->where('complectation_id', $request->get('complectation_id'));
    	$cars = $query->get();

    	foreach($cars as $itemCar) {
    		$itemCar->color->image = asset('storage/' . $itemCar->color->image . '?' . date('dmyh'));
    	}
    	return response()->json([
    		'data' => $cars,
    		'status' => 1
    	]);
    }

    public function show(Request $request)
    {
    	$query = Car::select('cars.*','brands.name as brand','marks.name as mark','complectations.name as complectation','car_prices.full_price')
    		->with('packs')
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

    // public function head(Request $request, $car = []) 
    // {
    	

    // 	if($request->has('car_id')) {
	   //  	$query = Car::select('cars.id','cars.vin','brands.name as brand','marks.name as mark','complectations.name as complectation','car_prices.full_price')
	   //  		->leftJoin('marks', 'marks.id', '=', 'cars.mark_id')
	   //  		->leftJoin('complectations', 'complectations.id', '=', 'cars.complectation_id')
	   //  		->leftJoin('car_prices', 'car_prices.car_id', '=', 'cars.id')
	   //  		->leftJoin('brands', 'brands.id', '=', 'cars.brand_id');
    // 		$query->where('cars.id', $request->get('car_id'));
    // 		$car = $query->first();
    // 	}
    	
    // 	return response()->json([
    // 		'data' => $car,
    // 		'status' => $car->count() ? 1 : 0
    // 	]);
    // }

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
}
