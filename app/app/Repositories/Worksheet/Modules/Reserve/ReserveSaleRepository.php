<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Models\WsmReserveCarSale;

class ReserveSaleRepository
{
    public function getSalesByReserveId(int $reserveId): \Illuminate\Database\Eloquent\Collection
    {
        $sales = WsmReserveCarSale::where('reserve_id', $reserveId)->get();

        return $sales;
    }



    public function save(WsmReserveCarSale $sale, array $data): void
    {
        $sale->fill(array_merge(
            $data,
            ['author_id' => auth()->user()->id]
        ))->save();
    }



    public function delete(WsmReserveCarSale $sale): void
    {
        $sale->delete();
    }
}
