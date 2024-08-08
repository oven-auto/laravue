<?php

namespace App\Services\Car\Option\Price;

class FactoryOptionPriceListStrategy
{
    public static function getStrategy(array $arr)
    {
        if (isset($arr['car_id']) && isset($arr['option_id']))
            return new StrategyCarOptionPriceList(carId: $arr['car_id'], optionId: $arr['option_id']);
        elseif (isset($arr['option_id']))
            return new StrategyOptionPriceList(optionId: $arr['option_id']);
        throw new \Exception('Не могу взять список цен, параметры не подходят');
    }
}
