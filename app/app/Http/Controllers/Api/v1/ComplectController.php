<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectController extends Controller
{
    public function __construct(\App\Repositories\ComplectationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $query = Complectation::with(['motor']);

        if($request->has('mark_id'))
            $query->where('mark_id', $request->get('mark_id'));

        $complectations = $query->get();

        if($complectations->count())
            return response()->json([
                'status' => 1,
                'data' => $complectations,
                'count' => $complectations->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одной комплектации'
        ]);
    }

    public function edit(Complectation $complectation)
    {
        $result = [];
        $complectation->devices;
        $complectation->packs;
        $complectation->colors;
        $complectation->colorPacks;
        $complectation->markColor;

        $coloredPacks = collect($complectation->packs->where('colored', 1)->all());

        foreach($complectation->markColor as $itemMarkColor) {
            $itemMarkColor->installColor = $complectation->colors->contains('id', $itemMarkColor->id) ? $itemMarkColor->id : 0;
            $itemMarkColor->image = asset('storage'.$itemMarkColor->image) . '?' .date('dmyhms');;
            $installColorPack = [];
            foreach($complectation->colorPacks as $itemColorPack) {
                if($itemColorPack->id == $itemMarkColor->id)
                    $installColorPack[] = $itemColorPack->pivot->pack_id;
            }
            $itemMarkColor->installColorPack = $installColorPack;

            $itemMarkColor->colorPackList = $coloredPacks;
        }

        return response()->json([
            'status' => 1,
            'data' => $complectation->toArray()
        ]);
    }

    public function store(Complectation $complectation, Request $request)
    {
        $this->repository->save($complectation, $request->all());
        return response()->json([
            'status' => 1,
            'data' => $complectation,
            'message' => 'Комплектация создана'
        ]);
    }

    public function update(Complectation $complectation, Request $request)
    {
        $this->repository->save($complectation, $request->all());
        return response()->json([
            'status' => 1,
            'data' => $complectation,
            'message' => 'Комплектация изменена'
        ]);
    }
}
