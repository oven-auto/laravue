<?php

namespace App\Http\Controllers\Api\v1\Back\Car;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\OverPrice\OverPriceRequest;
use App\Http\Requests\Car\CarCreateRequest;
use App\Http\Resources\Car\Car\CarItemResource;
use App\Http\Resources\Car\Car\CarListCollection;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Repositories\Car\Car\CarRepository;

class CarController extends Controller
{
    private $repo;

    public function __construct(CarRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * INDEX
     */
    public function index(Request $request)
    {
        $cars = $this->repo->paginate($request->all());

        return new CarListCollection($cars);
    }



    /**
     * STORE
     */
    public function store(CarCreateRequest $request)
    {
        $car = $this->repo->store($request->validated());
        //ЛОГИКА СОХРАНЕНИЯ ДАТЫ В ОБСЕРВЕРЕ
        return (new CarItemResource($car))
            ->additional(['message' => 'Автомобиль создан']);
    }



    /**
     * UPDATE
     */
    public function update(Car $car, CarCreateRequest $request)
    {
        $this->repo->update($car, $request->validated());
        //ЛОГИКА СОХРАНЕНИЯ ДАТЫ В ОБСЕРВЕРЕ
        return (new CarItemResource($car))
            ->additional(['message' => 'Автомобиль изменен']);
    }



    /**
     * SHOW
     */
    public function show(Car $car)
    {
        return new CarItemResource($car);
    }



    /**
     * OVER PRICE
     */
    public function makeOverPrice(Car $car, OverPriceRequest $request)
    {
        $this->repo->saveOverPrice($car, $request->price);

        return (new CarItemResource($car))
            ->additional(['message' => 'Дооценка зарегестрирована']);
    }



    public function getOverPrice(Car $car)
    {
        return response()->json([
            'data' => [
                'price' => $car->over_price->id ? $car->over_price->price : '',
                'author' => $car->over_price->id ? $car->over_price->author->cut_name : '',
                'date' => $car->over_price->id ? $car->updated_at->format('d.m.Y (H:i)') : '',
            ],
            'success' => 1,
        ]);
    }
}
