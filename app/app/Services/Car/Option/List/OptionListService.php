<?php

namespace App\Services\Car\Option\List;

use App\Models\Car;
use App\Repositories\Car\Option\OptionRepository;

class OptionListService
{
    private $repo;

    public function __construct(OptionRepository $repo)
    {
        $this->repo = $repo;
    }



    //Вернуть список опций
    public function getList(array $data, Car $car = null): \Illuminate\Support\Collection
    {
        $carOptionPrices = null;

        $options = $this->repo->get($data);

        if ($car) {
            $carOptionPrices = $car->getOptionCurrentPrices();
        }



        return $options->map(function ($item) use ($carOptionPrices) {
            $price = ($carOptionPrices) ? $carOptionPrices->where('option_id', $item->id)->first()->price : $item->current_price->price;
            $beginAt = '';

            if ($carOptionPrices) {
                $obj = $carOptionPrices->where('option_id', $item->id)->first();
                if ($obj instanceof \App\Models\OptionPrice)
                    $beginAt = $obj->begin_at->format('d.m.Y');
                elseif ($obj instanceof \App\Models\OptionCurrentPrice)
                    $beginAt = $obj->option_price->begin_at->format('d.m.Y');
            } else
                $beginAt = $item->current_price->option_price->begin_at ? $item->current_price->option_price->begin_at->format('d.m.Y') : '';

            return [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'price' => $price,
                'begin_at' => $beginAt,
                'mark' => $item->mark->name,
                'brand' => $item->brand->name,
                'trash' => (int)$item->trashed(),
                'author' => $item->author->cut_name,
                'created_at' => $item->created_at->format('d.m.Y',)
            ];
        });
    }
}
