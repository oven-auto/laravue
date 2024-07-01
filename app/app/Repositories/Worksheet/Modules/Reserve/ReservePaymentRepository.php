<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Models\WsmReservePayment;

class ReservePaymentRepository
{
    public function save(WsmReservePayment $pay, array $data): void
    {
        $pay->fill(array_merge(
            $data,
            ['author_id' => auth()->user()->id],
        ))->save();
    }



    public function delete(WsmReservePayment $pay): void
    {
        $pay->delete();
    }
}
