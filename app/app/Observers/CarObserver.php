<?php

namespace App\Observers;

use App\Classes\Notice\Notice;
use App\Models\Car;

class CarObserver
{
    public function created(Car $car)
    {
        //
    }



    public function updated(Car $car)
    {
        //Notice::setMessage('Автомобиль изменен');
    }
}
