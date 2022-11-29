<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Repositories\Car\CarRepository;
use App\Http\Resources\Car\CarListCollection;
use App\Http\Resources\Car\CarArchiveCollection;
use App\Http\Resources\Car\CarEditResource;
use App\Http\Resources\Car\CarArchiveEditResource;

class CarController extends Controller
{

    public function __construct(CarRepository $repository)
    {
        $this->repo = $repository;
    }

    public function index(Request $request)
    {
        $cars = $this->repo->filter($request->all(), 15)->withQueryString();
        if($request->has('archive'))
            return new CarArchiveCollection($cars);
        return new CarListCollection($cars);
    }

    public function edit(Car $car)
    {
        return new CarEditResource($car);
    }

    public function show($id)
    {
        $car = $this->repo->getTrashById($id);
        return new CarArchiveEditResource($car);
    }

    public function store(Car $car, Request $request)
    {
        $this->repo->save($car, $request->all());
        return new CarEditResource($car);
    }

    public function update(Car $car, Request $request)
    {
        $result = $this->repo->save($car, $request->all());
        return new CarEditResource($car);
    }

    public function destroy(Car $car, Request $request)
    {
        return $this->repo->delete($car);
    }
}
