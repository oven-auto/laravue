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
        $this->repository->getComplectationArray($complectation);

        return response()->json([
            'status' => 1,
            'data' => $complectation->toArray()
        ]);
    }

    public function store(Complectation $complectation, Request $request)
    {
        $result = $this->repository->save($complectation, $request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $complectation,
                'message' => 'Комплектация создана'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $complectation,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }

    public function update(Complectation $complectation, Request $request)
    {
        $result = $this->repository->save($complectation, $request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $complectation,
                'message' => 'Комплектация изменена'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $complectation,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }
}
