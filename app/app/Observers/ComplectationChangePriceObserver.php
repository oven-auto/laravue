<?php

namespace App\Observers;
use App\Models\Complectation;
use Auth;

class ComplectationChangePriceObserver
{
    public function saving(Complectation $complectation)
    {
        $newPrice = $complectation->price;
        $oldPrice = $complectation->getOriginal('price');

        $newPriceStatus = $complectation->price_status;
        $oldPriceStatus = $complectation->getOriginal('price_status');

        if (Auth::check()) {
            $userId = Auth::user()->id;
            if($newPrice != $oldPrice || $newPriceStatus != $oldPriceStatus)
                $complectation->moderator()->create(['user_id'=>$userId]);
        }
    }
}
