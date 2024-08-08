<?php

namespace App\Services\Car\Option\Price;

use App\Models\Car;

class StrategyCarOptionPriceList implements OptionPriceListInterface
{
    private $option;
    private $car;

    public function __construct(int $carId, int $optionId)
    {
        $this->car = Car::withTrashed()->findOrfail($carId);
        $this->option = $this->car->options->where('id', $optionId)->first();
    }



    public function getCar()
    {
        return $this->car;
    }



    public function getCurrentPrice()
    {
        return $this->car->getOptionCurrentPrices()->where('option_id', $this->option->id)->first();
    }



    public function getOption()
    {
        return $this->option;
    }



    public function getPriceList()
    {

        return $this->option->prices;
    }
}
