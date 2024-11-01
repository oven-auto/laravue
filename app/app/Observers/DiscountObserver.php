<?php

namespace App\Observers;

use App\Models\Discount;

class DiscountObserver
{
    public function created(Discount $discount)
    {
        $discount->check()->create([
            'discount_id' => $discount->id,
            'author_id' => auth()->user()->id,
        ]);
    }
}
