<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::with(['brand','color','mark','complectation'])->get();

        if($cars->count())
            return response()->json([
                'status' => 1,
                'data' => $cars,
                'count' => $cars->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного автомобиля'
        ]);
    }

    public function edit(Car $car)
    {
        $data['brand_id'] = $car->brand_id;
        $data['mark_id'] = $car->mark_id;
        $data['complectation_id'] = $car->complectation_id;
        $data['color_id'] = $car->color_id;
        $data['vin'] = $car->vin;
        $data['year'] = $car->year;
        $data['device_price'] = $car->device_price;
        $data['packs'] = $car->packs->pluck('id');
        $data['devices'] = $car->devices->pluck('id');

        return response()->json([
            'status' => 1,
            'data' =>$data
        ]);
    }

    public function store(Car $car, Request $request)
    {
        $car->fill($request->except(['devices', 'packs']))->save();
        $car->packs()->attach($request->get('packs'));
        $car->devices()->attach($request->get('devices'));

        return response()->json([
            'status' => 1,
            'data' => $car,
            'message' => 'Автомобиль создан'
        ]);
    }

    public function update(Car $car, Request $request)
    {
        return response()->json([
            'status' => 1,
            'data' => $car,
            'message' => 'Автомобиль изменен'
        ]);
    }
}
