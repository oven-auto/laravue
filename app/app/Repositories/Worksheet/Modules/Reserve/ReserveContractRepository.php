<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Classes\Notice\Notice;
use App\Events\DNMVisitEvent;
use App\Exceptions\Reserve\ReserveException;
use App\Http\Filters\ContractFilter;
use App\Models\WsmReserveNewCar;
use App\Models\WsmReserveNewCarContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReserveContractRepository
{
    /**
     * Фиксирование цены по дате дкп
     */
    public function fixCarPrice(WsmReserveNewCarContract $contract)
    {
        if(!$contract->dkp_offer_at)
            return;

        $this->fixComplectationPrice($contract);
        $this->fixOptionPrices($contract);
    }



    /**
     * Фиксирование цены комплектации по дате дкп
     */
    private function fixComplectationPrice(WsmReserveNewCarContract $contract)
    {
        $complectation = $contract->reserve->car->complectation;

        $complectationPrice = $complectation->prices
            ->sortBy('begin_at', SORT_NATURAL)->where('begin_at', '<=', $contract->dkp_offer_at)
            ->last();

        if (!$complectationPrice)
            throw new ReserveException('can_not_search_price');

        $contract->complectation_price()->sync(['complectation_price_id' => $complectationPrice->id]);
    }



    /**
     * Фиксирование цены опций по дате дкп
     */
    private function fixOptionPrices(WsmReserveNewCarContract $contract)
    {
        $ids = null;

        $options = $contract->reserve->car->options;

        $options->each(function ($item) use ($contract, &$ids) {
            $optionsPrice = $item->prices->sortBy('begin_at', SORT_NATURAL)->where('begin_at', '<=', $contract->dkp_offer_at)->last();

            if (!$optionsPrice)
                throw new ReserveException('can_not_search_price');

            $ids[] = $optionsPrice->id;
        });

        if ($ids)
            $contract->option_price()->sync($ids);
    }



    /**
     * ПРОВЕРКА ДОСТУПНОСТИ СОЗДАНИЯ КОНТРАКТА
     */
    public function checkMakeDKPOnCar(WsmReserveNewCarContract $contract, array $data)
    {
        $reserve = WsmReserveNewCar::findOrFail($data['reserve_id']);

        $car = $reserve->car;
        
        if(!$car->isOnStock() && isset($data['dkp_offer_at']) && $data['dkp_offer_at'])
            throw new ReserveException('contract_on_not_invoice');
    }



    public function checkCanDeleteContract(WsmReserveNewCarContract $contract, array $data)
    {
        if(isset($data['dkp_closed_at']) && !$contract->dkp_closed_at && $data['dkp_closed_at'] && $contract->reserve->isIssued())
            throw new ReserveException('contract_close');
    }



    private function save(WsmReserveNewCarContract $contract, array $data)
    {
        if(count($data) == 1)
            throw new ReserveException('create_contract_empty_data');
        
        $this->checkMakeDKPOnCar($contract, $data);

        $this->checkCanDeleteContract($contract, $data);

        try {
            DB::transaction(function () use ($contract, $data) {
                
                $contract->setEmpty();

                $contract->fill(array_merge(
                    $data,
                    ['author_id' => auth()->user()->id]
                ))->save();

                
                $this->fixCarPrice($contract);

                Notice::setMessage('Данные успешно приняты.');
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
            //if(Auth::id() != 47)
                throw new ReserveException('empty_client_data');


        if ($contractOld)
            throw new ReserveException('has_contract');

        $this->save($contract, $data);

        DNMVisitEvent::dispatch($reserve, 'contract');
    }



    public static function updateContract(WsmReserveNewCarContract $contract, array $data)
    {
        $service = new self;

        $service->fixCarPrice($contract);
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

        $contracts = $query->orderBy('wsm_reserve_new_car_contracts.id', 'DESC')->simplePaginate($paginate);

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
