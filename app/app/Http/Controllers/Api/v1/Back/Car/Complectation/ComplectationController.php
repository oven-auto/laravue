<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Complectation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Complectation\ComplectationGetRequest;
use App\Http\Requests\Car\Complectation\ComplectationRequest;
use App\Http\Requests\Car\Complectation\ComplectationSearchRequest;
use App\Http\Resources\Car\Complectation\ComplectationCollection;
use App\Http\Resources\Car\Complectation\ComplectationItemResource;
use App\Http\Resources\Car\Complectation\ComplectationSearchResource;
use App\Models\Complectation;
use App\Repositories\Car\Complectation\ComplectationRepository;

class ComplectationController extends Controller
{
    private $repo;

    public function __construct(ComplectationRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * INDEX
     * @param ComplectationGetRequest $request [mark_id | trash]
     * @return ComplectationCollection
     */
    public function index(ComplectationGetRequest $request)
    {
        $complectations = $this->repo->get($request->validated());

        return new ComplectationCollection($complectations);
    }



    /**
     * SEARCH
     * @param ComplectationSearchRequest $request [code | mark_id]
     * @return ComplectationSearchResource|\Illuminate\Http\JsonResponse
     */
    public function search(ComplectationSearchRequest $request) : ComplectationSearchResource | \Illuminate\Http\JsonResponse
    {
        $complectation = $this->repo->searchByCode($request->validated());

        if($complectation)
            return new ComplectationSearchResource($complectation);

        return response()->json(['success' => 0, 'message' => 'Совпадений не найдено'], 404);
    }



    /**
     * STORE NEW COMPLECTATION
     * @param ComplectationRequest $request [
     * 'code', 'name', 'mark_id', 'vehicle_type_id', 'body_work_id', 'factory_id', 'price', 'motor_id',
     * 'motor_transmission_id', 'motor_driver_id', 'motor_type_id', 'power', 'size', 'brand_id'
     * ]
     * @return ComplectationItemResource
     */
    public function store(ComplectationRequest $request) : ComplectationItemResource
    {
        $complectation = $this->repo->create($request->validated());

        return (new ComplectationItemResource($complectation))
            ->additional(['message' => 'Комплектация создана']);
    }



    /**
     * UPDATE
     * @param ComplectationRequest $request [
     * 'code', 'name', 'mark_id', 'vehicle_type_id', 'body_work_id', 'factory_id', 'price', 'motor_id',
     * 'motor_transmission_id', 'motor_driver_id', 'motor_type_id', 'power', 'size', 'brand_id'
     * @return ComplectationItemResource
     */
    public function update(Complectation $complectation, ComplectationRequest $request) : ComplectationItemResource
    {
        $this->repo->update($complectation, $request->validated());

        return (new ComplectationItemResource($complectation))
            ->additional(['message' => 'Комплектация изменена']);
    }



    /**
     * SHOW
     * @param Complectation $complectation
     * @return ComplectationItemResource
     */
    public function show(Complectation $complectation) : ComplectationItemResource
    {
        return new ComplectationItemResource($complectation);
    }



    /**
     * DELETE
     * @param Complectation $complectation
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Complectation $complectation) : \Illuminate\Http\JsonResponse
    {
        $this->repo->delete($complectation);

        return response()->json(['message' => 'Комплектация удалена', 'success' => 1]);
    }



    /**
     * RESTORE
     * @param Complectation $complectation
     * @return ComplectationItemResource
     */
    public function restore(Complectation $complectation) : ComplectationItemResource
    {
        $this->repo->restore($complectation);

        return (new ComplectationItemResource($complectation))
            ->additional(['message' => 'Комплектация актуальна']);
    }
}
