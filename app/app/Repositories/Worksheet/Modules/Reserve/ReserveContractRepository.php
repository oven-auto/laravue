<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Http\Filters\ContractFilter;
use App\Models\WsmReserveNewCar;
use App\Models\WsmReserveNewCarContract;

class ReserveContractRepository
{
    public function fixCarPrice(WsmReserveNewCarContract $contract)
    {
        $this->fixComplectationPrice($contract);
        $this->fixOptionPrices($contract);
    }



    private function fixComplectationPrice(WsmReserveNewCarContract $contract)
    {
        $complectation = $contract->reserve->car->complectation;

        $complectationPrice = $complectation->prices
            ->sortBy('begin_at', SORT_NATURAL)->where('begin_at', '<=', $contract->dkp_offer_at)
            ->last();

        if (!$complectationPrice)
            throw new \Exception('Под данную дату ДКП не могу выбрать подходящую цену комплектации');

        $contract->complectation_price()->sync(['complectation_price_id' => $complectationPrice->id]);
    }




    private function fixOptionPrices(WsmReserveNewCarContract $contract)
    {
        $ids = null;

        $options = $contract->reserve->car->options;

        $options->each(function ($item) use ($contract, &$ids) {
            $optionsPrice = $item->prices->sortBy('begin_at', SORT_NATURAL)->where('begin_at', '<=', $contract->dkp_offer_at)->last();

            if (!$optionsPrice)
                throw new \Exception('Под данную дату ДКП не могу выбрать подходящую цену опции');

            $ids[] = $optionsPrice->id;
        });

        if ($ids)
            $contract->option_price()->sync($ids);
    }



    private function save(WsmReserveNewCarContract $contract, array $data)
    {
        try {
            \DB::transaction(function () use ($contract, $data) {
                $oldDkpDate = $contract->dkp_offer_at;

                $contract->fill(array_merge(
                    $data,
                    ['author_id' => auth()->user()->id]
                ))->save();

                if (isset($data['dkp_offer_at']) && $oldDkpDate && $oldDkpDate->format('d.m.Y') != $data['dkp_offer_at']) {
                    $this->fixCarPrice($contract);
                }
            });
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }



    public function create(WsmReserveNewCarContract $contract, array $data): void
    {
        $contractOld = WsmReserveNewCarContract::where('reserve_id', $data['reserve_id'])->first();
        $reserve = WsmReserveNewCar::findOrFail($data['reserve_id']);
        $client = $reserve->worksheet->client;

        if (!$client->checkContractFields())
            throw new \Exception('В Вашей сделке, Вы дошли до заключения договора, однако у Вашего клиента не заполнены важные поля (ФИО/Компания, Зона контакта, Пол). Перейдите во вкладку клиент и дополните данные.');


        if ($contractOld)
            throw new \Exception('У резерва не может быть более одного контракта');

        $this->save($contract, $data);
    }



    public function update(WsmReserveNewCarContract $contract, array $data): void
    {
        $this->save($contract, $data);
    }



    /**
     * Paginator
     */
    public function paginate(array $data, $paginate = 20)
    {
        $query = WsmReserveNewCarContract::select('wsm_reserve_new_car_contracts.*')->with('reserve');

        $filter = app()->make(ContractFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $contracts = $query->simplePaginate($paginate);

        return $contracts;
    }



    /**
     * Counter
     */
    public function counter(array $data)
    {
        $query = WsmReserveNewCarContract::query()->select('wsm_reserve_new_car_contracts.id');

        $subQuery = WsmReserveNewCarContract::query()->select('wsm_reserve_new_car_contracts.id');

        $filter = app()->make(ContractFilter::class, ['queryParams' => array_filter($data)]);

        $subQuery->filter($filter);

        $query->rightJoinSub($subQuery, 'subQuery', function ($join) {
            $join->on('subQuery.id', '=', 'wsm_reserve_new_car_contracts.id');
        });

        $result = $query->count();

        return $result;
    }
}
