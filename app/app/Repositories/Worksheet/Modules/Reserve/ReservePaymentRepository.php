<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Models\Payment;
use App\Models\WsmReservePayment;

class ReservePaymentRepository
{
    public function save(WsmReservePayment $pay, array $data): void
    {
        $payment = Payment::find($data['payment_id']);

        if ($payment->isSubZero())
            $data['amount'] *= (-1);

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
