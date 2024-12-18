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
            where wsm_reserve_new_cars.car_id is not null and wsm_reserve_new_cars.deleted_at is null
            GROUP  BY  wsm_reserve_new_cars.car_id) as joinOptionPrice'), 'joinOptionPrice.car_id', 'cars.id'
        )
        ->addSelect([
            DB::raw('cars.id as carId'),
            DB::raw('(IF(complectation_prices.id,complectation_prices.price,car_full_prices.complectationprice)) as com_price'),
            DB::raw('(IF(joinOptionPrice.sum_option IS NOT NULL, joinOptionPrice.sum_option, car_full_prices.optionprice)) as opt_price'),
            DB::raw('(car_full_prices.overprice) as over_price'),
            DB::raw('(car_full_prices.tuningprice) as tun_price'),
            DB::raw('(car_full_prices.giftprice) as gift_price'),
            DB::raw('joinDiscount.amount as discount_sum'),
            DB::raw('joinPay.amount as payment_sum'),
            DB::raw('joinTradeIn.amount as tradein_sum'),
            DB::raw('IF(wsm_reserve_new_car_contracts.dkp_closed_at is null, 0, 1) as closed_at'),
        ]);

        $subQuery->leftJoin(DB::raw('(SELECT sum(ds.amount) as amount, d.modulable_id as reserveId from discounts as d
            left join discount_sums as ds on ds.discount_id = d.id
            where d.modulable_type = "App\\\Models\\\WsmReserveNewCar"
            group by reserveId) as joinDiscount'), 'joinDiscount.reserveId', 'wsm_reserve_new_cars.id');
       
        $subQuery->leftJoin(DB::raw('(SELECT sum(pay.amount) as amount, pay.reserve_id as reserveId
            from wsm_reserve_payments as pay group by reserveId) as joinPay'), 'joinPay.reserveId', 'wsm_reserve_new_cars.id');

        $subQuery->leftJoin(DB::raw('(SELECT sum(uc.purchase_price) as amount, t.reserve_id as reserveId
            from wsm_reserve_trade_ins as t
            left join used_cars as uc on uc.id = t.used_car_id) as joinTradeIn'), 'joinTradeIn.reserveId', 'wsm_reserve_new_cars.id');

        $subQuery->groupBy('wsm_reserve_new_car_contracts.id');

        $result = DB::table($subQuery)->select(
            DB::raw('count(*) as count'),
            DB::raw('(
                sum(com_price) + sum(opt_price) + sum(over_price) + 
                sum(tun_price) - sum(gift_price) - sum(discount_sum) - 
                sum(payment_sum) - sum(tradein_sum)) as debit'
            ),
            DB::raw('(
                sum(payment_sum) + sum(tradein_sum)) as credit'),
            // DB::raw('sum(com_price)     as _com_price'),
            // DB::raw('sum(opt_price)     as _opt_price'),
            // DB::raw('sum(over_price)    as _over_price'),
            // DB::raw('sum(tun_price)     as _tun_price'),
            // DB::raw('sum(gift_price)    as _gift_price'),
            // DB::raw('sum(discount_sum)  as _discount_sum'),
            // DB::raw('sum(payment_sum)   as _payment_sum'),
            // DB::raw('sum(tradein_sum)   as _tradein_sum'),
        )->first();
        
        return $result;
    }
}
