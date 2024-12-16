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
        $query = WsmReserveNewCarContract::select('wsm_reserve_new_car_contracts.*')
            ->with(['reserve' => function($subQ){
                $subQ->with([
                    'car' => function($qCar){
                        $qCar->with(['mark', 'brand', 'logistic_dates']);
                    },
                    'sale'
                ]);
            }]);

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
        //$query = WsmReserveNewCarContract::query()->select('wsm_reserve_new_car_contracts.id');

        $subQuery = WsmReserveNewCarContract::query();

        $filter = app()->make(ContractFilter::class, ['queryParams' => array_filter($data)]);

        $subQuery->filter($filter);

        $subQuery ->leftJoin('car_full_prices', 'car_full_prices.car_id', 'cars.id')//представление хранящее актуальную цену авто по прайсу
        ->leftJoin('wsm_reserve_complectation_prices','wsm_reserve_complectation_prices.contract_id', 'wsm_reserve_new_car_contracts.id')//сохраненая в контракте цена
        ->leftJoin('complectation_prices', 'complectation_prices.id', 'wsm_reserve_complectation_prices.complectation_price_id')//цены комплектации
        ->leftJoin('wsm_reserve_option_prices', 'wsm_reserve_option_prices.contract_id', 'wsm_reserve_new_car_contracts.id')//сохраненные в контракте опции
        ->leftJoin(DB::raw('(SELECT sum(option_prices.price) as sum_option, wsm_reserve_new_cars.car_id from option_prices 
            left join wsm_reserve_option_prices on wsm_reserve_option_prices.option_price_id = option_prices.id 
            left join wsm_reserve_new_car_contracts on wsm_reserve_new_car_contracts.id = wsm_reserve_option_prices.contract_id 
            left join wsm_reserve_new_cars on wsm_reserve_new_cars.id = wsm_reserve_new_car_contracts.reserve_id 
            where wsm_reserve_new_cars.car_id is not null and wsm_reserve_new_cars.deleted_at is not null
            GROUP  BY  wsm_reserve_new_cars.car_id) as joinOptionPrice'), 'joinOptionPrice.car_id', 'cars.id'
        )
        ->addSelect([
            DB::raw('COUNT(wsm_reserve_new_car_contracts.id) as count'),
            DB::raw('SUM(IF(complectation_prices.id,complectation_prices.price,car_full_prices.price)) as com_price'),
            DB::raw('SUM(IF(joinOptionPrice.sum_option IS NOT NULL, joinOptionPrice.sum_option, car_full_prices.optionprice)) as opt_price'),
            DB::raw('SUM(car_full_prices.overprice) as over_price'),
            DB::raw('SUM(car_full_prices.tuningprice) as tun_price'),
            DB::raw('SUM(car_full_prices.giftprice) as gift_price'),
        ]);

    //if(isset($params['has_discount']))
        $subQuery->leftJoin('discounts', function($join){
            $join->on('discounts.worksheet_id', '=', 'worksheets.id')
                ->on('discounts.modulable_type', '=', DB::raw('"App\\\Models\\\WsmReserveNewCar"'))
                ->on('discounts.modulable_id', 'wsm_reserve_new_cars.id');
        });

        // $query->rightJoinSub($subQuery, 'subQuery', function ($join) {
        //     $join->on('subQuery.id', '=', 'wsm_reserve_new_car_contracts.id');
        // });
        
        $result = $subQuery->get();

        return $result;
    }
}
