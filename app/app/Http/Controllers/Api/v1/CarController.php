<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Repositories\CarRepository;

class CarController extends Controller
{

    public function __construct(CarRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $cars = Car::with(['brand','color','mark','complectation','price'])->get();

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
        $data = $this->repository->getCarArray($car);

        return response()->json([
            'status' => 1,
            'data' =>$data
        ]);
    }

    public function store(Car $car, Request $request)
    {
        $result = $this->repository->save($car, $request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $car,
                'message' => 'Автомобиль создан'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $car,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }

    public function update(Car $car, Request $request)
    {
        $result = $this->repository->save($car, $request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $car,
                'message' => 'Автомобиль изменен'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $car,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }
}
