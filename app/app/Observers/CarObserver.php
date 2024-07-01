<?php

namespace App\Observers;

use App\Models\Car;
use App\Models\CarState;

class CarObserver
{
    public function saved(Car $car)
    {
        // $car->load('logistic_dates');

        // $dates = $car->logistic_dates;

        // $maxCoast = 0;

        // $maxDate = null;

        // foreach ($dates as $item) {
        //     if (abs($item->state->state) > $maxCoast) {
        //         $maxCoast = abs($item->state->state);

        //         $maxDate = $item;
        //     }
        // }

        // if ($maxDate) {
        //     $state = CarState::where('logistic_system_name', $maxDate->logistic_system_name)->first();

        //     Car::where('id', $car->id)->update(['status' => $state->status]);
        // }
    }
}
