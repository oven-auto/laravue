<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Modules\Reserve;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\Reserve\ReserveSaleListRequest;
use App\Http\Requests\Worksheet\Reserve\SaleSaveRequest;
use App\Http\Resources\Worksheet\Reserve\SaleReserveItemResource;
use App\Http\Resources\Worksheet\Reserve\SaleReserveResource;
use App\Models\WsmReserveCarSale;
use App\Repositories\Worksheet\Modules\Reserve\ReserveSaleRepository;

class SaleController extends Controller
{
    private $repo;

    public function __construct(ReserveSaleRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(ReserveSaleListRequest $request)
    {
        $sales = $this->repo->getSalesByReserveId($request->reserve_id);

        return response()->json([
            'data' => SaleReserveItemResource::collection($sales),
            'success' => 1,
        ]);
    }



    public function store(WsmReserveCarSale $sale, SaleSaveRequest $request)
    {
        $this->repo->save($sale, $request->validated());

        return (new SaleReserveResource($sale))->additional([
            'message' => 'Скидка добавлена.',
        ]);
    }



    public function show(WsmReserveCarSale $sale)
    {
        return new SaleReserveResource($sale);
    }



    public function update(WsmReserveCarSale $sale, SaleSaveRequest $request)
    {
        $this->repo->save($sale, $request->validated());

        return (new SaleReserveResource($sale))->additional([
            'message' => 'Скидка изменена.',
        ]);
    }



    public function destroy(WsmReserveCarSale $sale)
    {
        $this->repo->delete($sale);

        return response()->json([
            'message' => 'Скидка отменена',
            'success' => 1,
        ]);
    }
}
