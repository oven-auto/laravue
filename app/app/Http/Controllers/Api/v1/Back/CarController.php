<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Repositories\CarRepository;
use App\Http\Filters\CarFilter;

class CarController extends Controller
{

    public function __construct(CarRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $query = Car::with(['brand','color','mark','complectation','price','delivery_stage.stage']);
        $filter = app()->make(CarFilter::class, ['queryParams' => array_filter($data)]);
        $cars = $query->filter($filter)->paginate(20);

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

    public function show($id)
    {
        $data = Car::withTrashed()->with(['brand','complectation','devices','packs','mark','color','price','fixedprice'])->find($id);
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

    public function destroy(Car $car, Request $request)
    {
        $car->fixedprice()->updateOrCreate(
            [
                'car_id'=>$car->id
            ],
            [
                'body_price'=>$car->complectation->price,
                'packs_price' => $car->price->pack_price,
                'equipments_price' => $car->device_price
            ]
        );
        $car->delete();
        return response()->json([
            'data'=>1
        ]);
    }
}
