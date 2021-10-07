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
    	$query = Car::with(['brand','mark','complectation.devices','color','packs','devices','price']);
    	if($request->has('car_id')) 
    		$query->where('id', $request->get('car_id'));
    	$car = $query->first();

    	$car->color->image = asset('storage/' . $car->color->image . '?' . date('dmyh'));

    	return response()->json([
    		'data' => $car,
    		'status' => 1
    	]);
    }
}
