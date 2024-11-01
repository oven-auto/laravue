<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Complectation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Complectation\ComplectationPriceListRequest;
use App\Http\Requests\Car\Complectation\PriceComplectationSaveRequest;
use App\Models\ComplectationPrice;
use App\Repositories\Car\Complectation\PriceComplectationRepository;

class PriceComplectationController extends Controller
{
    private $repo;

    public function __construct(PriceComplectationRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(ComplectationPriceListRequest $request)
    {
        $result = $this->repo->get($request->validated());

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }



    public function store(ComplectationPrice $complectationprice, PriceComplectationSaveRequest $request)
    {
        $this->repo->save($complectationprice, $request->validated());

        return response()->json([
            'data' => [
                'id'            => $complectationprice->id,
                'price'         => $complectationprice->price,
                'begin_at'      => $complectationprice->begin_at->format('d.m.Y'),
                'author'        => $complectationprice->author->cut_name,
                'created_at'    => $complectationprice->created_at->format('d.m.Y'),
            ],
            'success' => 1,
        ]);
    }



    public function update(ComplectationPrice $complectationprice, PriceComplectationSaveRequest $request)
    {
        return response()->json([
            'message' => 'Изменение цены не допустимо',
            'success' => 0,
        ]);
    }



    public function show(ComplectationPrice $complectationprice)
    {
        return response()->json([
            'data' => [
                'id'            => $complectationprice->id,
                'price'         => $complectationprice->price,
                'begin_at'      => $complectationprice->begin_at->format('d.m.Y'),
                'author'        => $complectationprice->author->cut_name,
                'created_at'    => $complectationprice->created_at->format('d.m.Y'),
            ],
            'success' => 1,
        ]);
    }
}
