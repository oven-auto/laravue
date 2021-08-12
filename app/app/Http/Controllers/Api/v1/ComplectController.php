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

    public function index()
    {
        $complectations = Complectation::get();
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
        $complectation->devices;
        $complectation->packs;
        $complectation->colors;
        $complectation->colorPacks;

        return response()->json([
            'status' => 1,
            'data' => $complectation->toArray()
        ]);
    }

    public function store(Complectation $complectation, Request $request)
    {
        $this->repository->save($complectation, $request->all());
    }

    public function update(Complectation $complectation, Request $request)
    {

    }
}
