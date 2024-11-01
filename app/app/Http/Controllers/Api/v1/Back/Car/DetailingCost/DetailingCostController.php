<?php

namespace App\Http\Controllers\Api\v1\Back\Car\DetailingCost;

use App\Http\Controllers\Controller;
use App\Models\DetailingCost;
use App\Repositories\Car\DetailingCost\DetailingCostRepository;
use App\Http\Requests\Car\DetailingCost\DetailingCostRequest;
use App\Http\Resources\Car\DetailingCost\DetailingCostCollection;
use App\Http\Resources\Car\DetailingCost\DetailingCostItemResource;
use Illuminate\Http\Request;

class DetailingCostController extends Controller
{
    private $repo;

    public function __construct(DetailingCostRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(Request $request)
    {
        $validated = $request->validate([
            'trash' => 'sometimes'
        ]);

        $costs = $this->repo->get($validated);

        return new DetailingCostCollection($costs);
    }   
    
    

    public function store(DetailingCost $detailingcost, DetailingCostRequest $request)
    {
        $this->repo->save($detailingcost, $request->validated());

        return (new DetailingCostItemResource($detailingcost))
            ->additional(['message' => 'Детализация цены создана']);
    }



    public function update(DetailingCost $detailingcost, DetailingCostRequest $request)
    {
        $this->repo->save($detailingcost, $request->validated());

        return (new DetailingCostItemResource($detailingcost))
            ->additional(['message' => 'Детализация цены изменена']);
    }



    public function show(DetailingCost $detailingcost)
    {
        return (new DetailingCostItemResource($detailingcost));
    }



    public function delete(DetailingCost $detailingcost)
    {
        $this->repo->delete($detailingcost);

        return response()->json([
            'data' => 'Детализация цены удалена',
            'success' => 1,
        ]);
    }



    public function restore(DetailingCost $detailingcost)
    {
        $this->repo->restore($detailingcost);

        return response()->json([
            'data' => 'Детализация цены востановлена',
            'success' => 1,
        ]);
    }
}
