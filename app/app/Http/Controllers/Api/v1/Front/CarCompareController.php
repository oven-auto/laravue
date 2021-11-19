<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;

class CarCompareController extends Controller
{
    public function compare(Request $request) 
    {
    	$cars = [];
    	$equipments = [];
    	
    	if($request->has('car_ids')) {
    		$carIds = explode(',', $request->get('car_ids'));
    		$cars = Car::with(['brand','complectation', 'mark', 'color', 'price', 'equipments'])->whereIn('id', $carIds)->get();
    		foreach($cars as $key => $itemCar) {
    			if(strpos($itemCar->color->image, asset('storage')) === false)
    				$itemCar->color->image = asset('storage/' . $itemCar->color->image . '?' . date('dmyh'));
    			foreach ($itemCar->equipments as $key => $itemEquipment) {
    				$equipments[$itemEquipment->id] = $itemEquipment->name;
    			}
    		}
    	}
    	
    	return response()->json([
    		'cars' => $cars,
    		'status' => count($cars)?1:0,
    		'equipments' => $equipments
    	]);
    }
}
