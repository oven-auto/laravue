<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Color\ColorCreateRequest;
use App\Http\Requests\Car\Color\ColorIndexRequest;
use App\Http\Requests\Car\Color\ColorListRequest;
use App\Http\Resources\Car\Color\ColorCollection;
use App\Http\Resources\Car\Color\ColorEditResource;
use App\Http\Resources\Car\Color\ColorListResource;
use App\Models\DealerColor;
use App\Repositories\Car\Color\ColorRepository;

class ColorController extends Controller
{
    private $repo;

    public function __construct(ColorRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * GET COLORS
     * @param ColorIndexRequest $request [mark_id | trash | name]
     * @return ColorCollection
     */
    public function index(ColorIndexRequest $request) : ColorCollection
    {
        $colors = $this->repo->get($request->all());

        return new ColorCollection($colors);
    }



    /**
     * LIST
     * @param ColorListRequest $request [mark_id]
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function list(ColorListRequest $request) : \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $list = $this->repo->list($request->mark_id);

        return (ColorListResource::collection($list))
            ->additional(['success' => 1]);
    }



    /**
     * STORE
     * @param ColorCreateRequest $request [brand_id, mark_id, base_id, name]
     * @return ColorEditResource
     */
    public function store(ColorCreateRequest $request) : ColorEditResource
    {
        $dealercolor = $this->repo->store($request->all());

        return (new ColorEditResource($dealercolor))
            ->additional(['message' => 'Цвет создан']);
    }



    /**
     * UPDATE
     * @param DealerColor $dealercolor
     * @param ColorCreateRequest $request [brand_id, mark_id, base_id, name]
     */
    public function update(DealerColor $dealercolor, ColorCreateRequest $request)
    {
        $this->repo->update($dealercolor, $request->all());

        return (new ColorEditResource($dealercolor))
            ->additional(['message' => 'Цвет изменен']);
    }



    /**
     * SHOW
     * @param DealerColor $dealercolor
     * @return ColorEditResource
     */
    public function show(DealerColor $dealercolor) : ColorEditResource
    {
        return new ColorEditResource($dealercolor);
    }



    /**
     * DELETE
     * @param DealerColor $dealercolor
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DealerColor $dealercolor) : \Illuminate\Http\JsonResponse
    {
        $this->repo->delete($dealercolor);

        return response()->json(['message' => 'Цвет удален', 'success' => 1]);
    }



    /**
     * RESTORE
     * @param DealerColor $dealercolor
     * @return ColorEditResource
     */
    public function restore(DealerColor $dealercolor) : ColorEditResource
    {
        $this->repo->restore($dealercolor);

        return (new ColorEditResource($dealercolor))
            ->additional(['message' => 'Цвет актуален']);
    }
}
