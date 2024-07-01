<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Models\WsmReserveNewCarContract;

class ReserveContractRepository
{
    public function paginate()
    {
    }



    private function save(WsmReserveNewCarContract $contract, array $data): void
    {
        $oldPdkpDate = $contract->pdkp_offer_at;

        $contract->fill(array_merge(
            $data,
            ['author_id' => auth()->user()->id]
        ))->save();

        if ($oldPdkpDate->format('d.m.Y') != $data['dkp_offer_at']) {
            $complectation = $contract->reserve->car->complectation;
            $options = $contract->reserve->car->options;

            $complectationPrice = $complectation->prices
                ->sortBy('begin_at', SORT_NATURAL)->where('begin_at', '<=', $contract->dkp_offer_at)
                ->last()->price ?? 0;

            $optionsPrice = 0;
            $options->each(function ($item) use (&$optionsPrice, $contract) {
                $optionsPrice += $item->prices->sortBy('begin_at', SORT_NATURAL)->where('begin_at', '<=', $contract->dkp_offer_at)->last()->price ?? 0;
            });

            $contract->contract_cost()->updateOrCreate(
                ['contract_id' => $contract->id],
                [
                    'author_id' => auth()->user()->id,
                    'complectation' => $complectationPrice,
                    'option' => $optionsPrice
                ]
            );
        }
    }



    public function create(WsmReserveNewCarContract $contract, array $data): void
    {
        if (WsmReserveNewCarContract::where('reserve_id', $data['reserve_id'])->first())
            throw new \Exception('У резерва не может быть более одного контракта');

        $this->save($contract, $data);
    }



    public function update(WsmReserveNewCarContract $contract, array $data): void
    {
        $this->save($contract, $data);
    }
}
