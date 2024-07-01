<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Option;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Option\OptionCreateRequest;
use App\Http\Requests\Car\Option\OptionIndexRequest;
use App\Http\Resources\Car\Option\OptionCollection;
use App\Http\Resources\Car\Option\OptionItemResource;
use App\Repositories\Car\Option\OptionRepository;
use App\Models\Option;

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
     * @return OptionCollection
     */
    public function index(OptionIndexRequest $request) : OptionCollection
    {
        $options = $this->repo->get($request->all());

        return new OptionCollection($options);
    }



    /**
     * STORE
     * @param OptionCreateRequest $request ['name', 'code', 'price', 'brand_id', 'mark_id']
     * @return OptionItemResource
     */
    public function store(OptionCreateRequest $request) : OptionItemResource
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
    public function update(Option $option, OptionCreateRequest $request) : OptionItemResource
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
    public function show(Option $option) : OptionItemResource
    {
        return (new OptionItemResource($option));
    }



    /**
     * DELETE
     * @param Option $option
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Option $option) : \Illuminate\Http\JsonResponse
    {
        $this->repo->delete($option);

        return response()->json(['message' => 'Опция удалена', 'success' => 1]);
    }



    /**
     * RESTORE
     * @param Option $option
     * @return OptionItemResource
     */
    public function restore(Option $option) : OptionItemResource
    {
        $this->repo->restore($option);

        return (new OptionItemResource($option))
            ->additional(['message' => 'Опция актуальна']);;
    }
}
