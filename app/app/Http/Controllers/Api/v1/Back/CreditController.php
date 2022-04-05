<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Credit;
use App\Repositories\CreditRepository;

class CreditController extends Controller
{

    public function __construct(CreditRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $credits = Credit::with('marks')->get();
        if($credits->count())
            return response()->json([
                'status' => 1,
                'data' => $credits,
                'count' => $credits->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного кредита'
        ]);
    }

    public function edit(Credit $credit)
    {
        $data = $this->repository->getCreditArray($credit);

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function store(Credit $credit, Request $request)
    {
        $result = $this->repository->save($credit, $request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $credit,
                'message' => 'Кредит создан'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $credit,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }

    public function update(Credit $credit, Request $request)
    {
        $result = $this->repository->save($credit, $request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $credit,
                'message' => 'Кредит изменен'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $credit,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }
}
