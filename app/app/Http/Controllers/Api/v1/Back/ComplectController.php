<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;
use App\Http\Filters\ComplectationFilter;

class ComplectController extends Controller
{
    public function __construct(\App\Repositories\ComplectationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $query = Complectation::with(['motor','brand','mark']);
        $filter = app()->make(ComplectationFilter::class, ['queryParams' => array_filter($data)]);
        $complectations = $query->filter($filter)
            ->orderBy('mark_id')
            ->orderBy('sort')
            ->get();

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

    public function edit($complectation)
    {
        if($complectation) {
            $complectation = Complectation::find($complectation);
            $this->repository->getComplectationArray($complectation);
            return response()->json([
                'status' => 1,
                'data' => $complectation->toArray()
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'data' => 'Не нашлось'
            ]);
        }

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
