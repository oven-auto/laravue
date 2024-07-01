<?php

namespace App\Repositories\Car\Option;

use App\Models\OptionPrice;
use App\Repositories\Car\Option\DTO\PriceOptionDTO;

class PriceOptionRepository
{
    public function get(array $data = [])
    {
        $query = OptionPrice::query();

        if (isset($data['option_id']))
            $query->where('option_id', $data['option_id']);

        $prices = $query->get();

        return $prices;
    }



    public function save(OptionPrice $optionPrice, array $data)
    {
        $optionPrice->fill(array_merge(
            (new PriceOptionDTO($data))->get(),
            ['author_id' => auth()->user()->id]
        ))->save();
    }
}
