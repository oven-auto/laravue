<?php

namespace App\Http\Controllers\Api\v1\Back\Car\TradeMarker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Marker\MarkerCreateRequest;
use App\Http\Resources\Car\Marker\MarkerCollection;
use App\Http\Resources\Car\Marker\MarkerItemResource;
use App\Models\TradeMarker;
use App\Repositories\Car\TradeMarker\TradeMarkerRepository;
use Illuminate\Http\Request;

class TradeMarkerController extends Controller
{
    private $repo;

    public function __construct(TradeMarkerRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * GET
     * @return MarkerCollection
     */
    public function index(Request $request): MarkerCollection
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
    public function store(TradeMarker $marker, MarkerCreateRequest $request): MarkerItemResource
    {
        $marker = $this->repo->save($marker, $request->validated());

        return (new MarkerItemResource($marker))
            ->additional(['message' => 'Маркер добавлен']);
    }



    /**
     * UPDATE
     * @param TradeMarker $marker
     * @param MarkerCreateRequest $request ['name', 'text_color', 'body_color', 'description']
     * @return MarkerItemResource
     */
    public function update(TradeMarker $marker, MarkerCreateRequest $request): MarkerItemResource
    {
        $this->repo->save($marker, $request->validated());

        return (new MarkerItemResource($marker))
            ->additional(['message' => 'Маркер изменен']);
    }



    /**
     * SHOW
     * @return MarkerItemResource
     */
    public function show(TradeMarker $marker): MarkerItemResource
    {
        return (new MarkerItemResource($marker));
    }



    /**
     * DELETE
     * @param TradeMarker $marker
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(TradeMarker $marker): \Illuminate\Http\JsonResponse
    {
        $this->repo->delete($marker);

        return response()->json(['message' => 'Маркер удален', 'success' => 1]);
    }



    /**
     * RESTORE
     * @param TradeMarker $marker
     * @return MarkerItemResource
     */
    public function restore(TradeMarker $marker): MarkerItemResource
    {
        $this->repo->restore($marker);

        return (new MarkerItemResource($marker))
            ->additional(['message' => 'Маркер актуален']);
    }
}
