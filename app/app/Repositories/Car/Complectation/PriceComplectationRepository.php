<?php

namespace App\Repositories\Car\Complectation;

use App\Models\ComplectationPrice;
use App\Repositories\Car\Complectation\DTO\PriceComplectationDTO;

class PriceComplectationRepository
{
    public function get(array $data = null)
    {
        $query = ComplectationPrice::query();

        $query->where('complectation_id', $data['complectation_id']);

        $result = $query->get();

        return $result;
    }



    public function save(ComplectationPrice $complectationPrice, array $data)
    {
        $complectationPrice->fill(array_merge(
            (new PriceComplectationDTO($data))->get(),
            ['author_id' => auth()->user()->id]
        ))->save();
    }
}
