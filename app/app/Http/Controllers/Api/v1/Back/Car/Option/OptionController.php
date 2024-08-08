<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Option;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Option\OptionCreateRequest;
use App\Http\Requests\Car\Option\OptionIndexRequest;
use App\Http\Resources\Car\Option\OptionItemResource;
use App\Models\Car;
use App\Repositories\Car\Option\OptionRepository;
use App\Models\Option;
use App\Services\Car\Option\List\OptionListService;

class OptionController extends Controller
{
    private $repo;

    public function __construct(OptionRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * INDEX
     * @param OptionIndexRequest $request [trash, mark_id, name, code]
     *
     */
    public function index(OptionIndexRequest $request, OptionListService $service)
    {
        $car = $request->has('car_id') ? Car::findOrFail($request->car_id) : null;

        $res = $service->getList($request->all(), $car);

        return response()->json([
            'data' => [
                'options' => $res,
                'car' => $car ? [
                    'vin' => $car->vin ?? '',
                    'brand' => $car->brand->name,
                    'mark' => $car->mark->name,
                    'vehicle_type' => $car->complectation->vehicle->name,
                    'body' => $car->complectation->bodywork->name,
                ] : [],
            ],
            'success' => 1
        ]);
    }



    /**
     * STORE
     * @param OptionCreateRequest $request ['name', 'code', 'price', 'brand_id', 'mark_id']
     * @return OptionItemResource
     */
    public function store(OptionCreateRequest $request): OptionItemResource
    {
        $option = $this->repo->store($request->all());

        return (new OptionItemResource($option))
            ->additional(['message' => 'Опция добавлена']);
    }



    /**
     * UPDATE
     * @param Option $option
     * @param OptionCreateRequest $request ['name', 'code', 'price', 'brand_id', 'mark_id']
     * @return OptionItemResource
     */
    public function update(Option $option, OptionCreateRequest $request): OptionItemResource
    {
        $this->repo->update($option, $request->all());

        return (new OptionItemResource($option))
            ->additional(['message' => 'Опция изменена']);
    }



    /**
     * SHOW
     * @param Option $option
     * @return OptionItemResource
     */
    public function show(Option $option): OptionItemResource
    {
        return (new OptionItemResource($option));
    }



    /**
     * DELETE
     * @param Option $option
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Option $option): \Illuminate\Http\JsonResponse
    {
        $this->repo->delete($option);

        return response()->json(['message' => 'Опция удалена', 'success' => 1]);
    }



    /**
     * RESTORE
     * @param Option $option
     * @return OptionItemResource
     */
    public function restore(Option $option): OptionItemResource
    {
        $this->repo->restore($option);

        return (new OptionItemResource($option))
            ->additional(['message' => 'Опция актуальна']);;
    }
}
