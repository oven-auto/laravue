<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Marker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Marker\MarkerCreateRequest;
use App\Http\Resources\Car\Marker\MarkerCollection;
use App\Http\Resources\Car\Marker\MarkerItemResource;
use App\Repositories\Car\Marker\MarkerRepository;
use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerController extends Controller
{
    private $repo;

    public function __construct(MarkerRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * GET
     * @return MarkerCollection
     */
    public function index(Request $request) : MarkerCollection
    {
        $validated = $request->validate([
            'trash' => 'sometimes|numeric'
        ]);

        $markers = $this->repo->get($validated);

        return new MarkerCollection($markers);
    }



    /**
     * STORE
     * @param MarkerCreateRequest $request ['name', 'text_color', 'body_color', 'description']
     * @return MarkerItemResource
     */
    public function store(MarkerCreateRequest $request) : MarkerItemResource
    {
        $marker = $this->repo->store($request->validated());

        return (new MarkerItemResource($marker))
            ->additional(['message' => 'Маркер добавлен']);
    }



    /**
     * UPDATE
     * @param Marker $marker
     * @param MarkerCreateRequest $request ['name', 'text_color', 'body_color', 'description']
     * @return MarkerItemResource
     */
    public function update(Marker $marker, MarkerCreateRequest $request) : MarkerItemResource
    {
        $this->repo->update($marker, $request->all());
        
        return (new MarkerItemResource($marker))
            ->additional(['message' => 'Маркер изменен']);
    }



    /**
     * SHOW
     * @return MarkerItemResource
     */
    public function show(Marker $marker) : MarkerItemResource
    {
        return (new MarkerItemResource($marker));
    }



    /**
     * DELETE
     * @param Marker $marker
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Marker $marker) : \Illuminate\Http\JsonResponse
    {
        $this->repo->delete($marker);

        return response()->json(['message' => 'Маркер удален', 'success' => 1]);
    }



    /**
     * RESTORE
     * @param Marker $marker
     * @return MarkerItemResource
     */
    public function restore(Marker $marker) : MarkerItemResource
    {
        $this->repo->restore($marker);

        return (new MarkerItemResource($marker))
            ->additional(['message' => 'Маркер актуален']);
    }
}
