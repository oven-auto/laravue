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

    public function index(Request $request)
    {
        $query = Car::with(['brand','color','mark','complectation','price']);

        if($request->has('brand_id'))
            $query->where('brand_id', $request->get('brand_id'));
        if($request->has('mark_id'))
            $query->where('mark_id', $request->get('mark_id'));
        if($request->has('complectation_id'))
            $query->where('complectation_id', $request->get('complectation_id'));
        if($request->has('vin'))
            $query->where('vin', 'like', '%'.$request->get('vin').'%');

        $cars = $query->paginate(20);

        foreach($cars as $itemCar)
            if(strpos($itemCar->color->image, asset('storage')) === false)
                $itemCar->color->image =  $itemCar->color->image_date;

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
