<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Classes\Wait\Wait;
use App\Events\DNMVisitEvent;
use App\Exceptions\Reserve\ReserveException;
use App\Http\Filters\ReserveNewCarFilter;
use App\Models\Car;
use App\Models\DealerColorImage;
use App\Models\WsmReserveNewCar;
use App\Models\WsmReserveNewCarContract;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReserveRepository
{
    public function isFreeCar(int $carId)
    {
        $car = WsmReserveNewCar::where('car_id', $carId)->first();

        if ($car)
            return 0;
        return 1;
    }


    
    /**
     * Зафиксировать Выдачу/Продажу
     */
    public function saveDealDate(WsmReserveNewCar $reserve, array $data)
    {
        $arr = [
            'decorator_id'  => $data['decorator_id'],
            'date_at'       => $data['date_at'],
            'author_id'     => auth()->user()->id,
        ];

        match($data['type']){
            'sale' => $this->fixSaleDate($reserve, $arr),
            'issue' => $this->fixIssueDate($reserve, $arr),
            default => throw new ReserveException('deal_date_type_error'),
        };
    }



    /**
     * Фиксировать дату продажи
     */
    public function fixSaleDate(WsmReserveNewCar $reserve, array $data)
    {
        if(!$reserve->isIssued() || !$reserve->car->hasPTS())
            throw new ReserveException('sale_error');
        $reserve->sale()->updateOrCreate(['reserve_id' => $reserve->id], $data);

        DNMVisitEvent::dispatch($reserve, 'issue');
    }



    /**
     * Фиксировать дату выдачи
     */
    public function fixIssueDate(WsmReserveNewCar $reserve, array $data)
    {
        if(!$reserve->contract->dkp_offer_at || $reserve->contract->dkp_closed_at)
            throw new ReserveException('issue_error');
        $reserve->issue()->updateOrCreate(['reserve_id' => $reserve->id], $data);
    }



    /**
     * Удаление дат сделки
     */
    public function deleteDealDate(WsmReserveNewCar $reserve, array $data)
    {
        match($data['type']){
            'sale' => $this->deleteSaleDate($reserve),
            'issue' => $this->deleteIssueDate($reserve),
            default => throw new ReserveException('deal_date_type_error'),
        };
    }



    /**
     * Удалить дату продажи
     */
    public function deleteSaleDate(WsmReserveNewCar $reserve)
    {
        if($reserve->worksheet->isClosing())
            throw new ReserveException('delete_sale');
        $reserve->sale->delete();
    }



    /**
     * Удалить дату выдачи
     */
    public function deleteIssueDate(WsmReserveNewCar $reserve)
    {
        if($reserve->isSaled())
            throw new ReserveException('delete_issue');
        $reserve->issue->delete();
    }



    /**
     * СОЗДАТЬ НОВЫЙ РЕЗЕРВ
     */
    public function createReserve(array $data): WsmReserveNewCar
    {
        if (!$this->isFreeCar($data['car_id']))
            throw new ReserveException('reserve_car');

        $reserve = WsmReserveNewCar::create(array_merge(
            $data,
            ['author_id' => auth()->user()->id]
        ));

        return $reserve;
    }



    public function changeCar(WsmReserveNewCar $reserve, array $data)
    {
        $currentCar = $reserve->car;

        $newCar = Car::find($data['car_id']);

        try{
            if($reserve->hasPDKP() && $reserve->hasDKP() && !$newCar->isOnStock())
            {
                $reserve->contract->fill([
                    'dkp_offer_at' => null,
                    'dkp_decorator_id' => null,
                ])->save();
            }
            elseif(!$reserve->hasPDKP() && $reserve->hasDKP() && !$newCar->isOnStock())
            {
                $reserve->contract->fill([
                    'dkp_offer_at' => null,
                    'dkp_decorator_id' => null,
                    'pdkp_offer_at' => $reserve->contract->dkp_offer_at,
                    'pdkp_decorator_id' => $reserve->contract->dkp_decorator_id
                ])->save();
            }

            $reserve->fill(['car_id' => $newCar->id])->save();

            $reserve->car = $newCar;
            
            ReserveContractRepository::updateContract($reserve->contract, $reserve->contract->toArray());
        } catch(Throwable $e){
            $reserve->fill(['car_id' => $currentCar->id])->save();
            throw new ReserveException($e->getMessage());
        }
    }



    /**
     * ЗАМЕНИТЬ АВТОМОБИЛЬ В РЕЗЕРВЕ
     */
    public function changeCarInReserve(WsmReserveNewCar $reserve, array $data): void
    {
        if (!$this->isFreeCar($data['car_id']))
            throw new ReserveException('reserve_car');

        if($reserve->isIssued())
            throw new ReserveException('has_issue');

        if($reserve->isClosedContract())
            throw new ReserveException('closed_contract');

        $this->changeCar($reserve, $data);
    }



    /**
     * УДАЛИТЬ РЕЗЕРВ
     */
    public function deleteReserve(WsmReserveNewCar $reserve): void
    {
        if ($reserve->contract->isWorking() && isset($reserve->contract->id))
            throw new ReserveException('has_open_contract');

        if($reserve->sale)
            throw new ReserveException('has_sale');

        if($reserve->issue)
            throw new ReserveException('has_issue');

        $reserve->delete();
    }



    /**
     * Добавить трейдын в резерв
     */
    public function attachTradeIn(WsmReserveNewCar $reserve, array $data)
    {
        $reserve->tradeins()->sync($data);
    }



    public function paginate(array $data, $paginate = 20)
    {
        $query = WsmReserveNewCar::select('wsm_reserve_new_cars.*');

        if(DealerColorImage::select(DB::raw('count(id) as count'))->first()->toArray()['count'])
            $query->withDataForReserveList();

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);
        
        $reserves = $query->orderBy('wsm_reserve_new_cars.id', 'DESC')->simplePaginate($paginate);

        if(DealerColorImage::select(DB::raw('count(id) as count'))->first()->toArray()['count'] == 0)
            $reserves->each(function($item) {
                $item->car->reserve = $item;
            });
        
        return $reserves;
    }



    public function get(array $data, $limit = 1000)
    {
        $query = WsmReserveNewCar::select('wsm_reserve_new_cars.*');

        $query->withDataForReserveList();

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);
        
        $reserves = $query->orderBy('wsm_reserve_new_cars.id', 'DESC')->get($limit);

        $reserves->each(function($item) {
            $item->car->reserve = $item;
        });
        
        return $reserves;
    }



    public function counter(array $data)
    {   
        $query = WsmReserveNewCar::query()
            ->select(
                '_car.id', 
                '_full_price.tuningprice as t_price', 
                '_full_price.overprice as ov_price',
                '_full_price.giftprice as gift_price',
                DB::raw('IF(_cp.price IS NOT NULL, _cp.price, _full_price.complectationprice) as com_price'),
                DB::raw('IF(_joinOptionPrice._sum_option IS NOT NULL, _joinOptionPrice._sum_option, _full_price.optionprice) as op_price'),
                DB::raw('sum(_disc_sum.amount) as discount_price'),

                //'cars.disable_off as _disable',
                '_owner.id as owner_count',
                DB::raw('IF(_car.disable_off, _car.disable_off, 0) as _disable'),
                DB::raw('IF(_owner.client_id = IF(_w.client_id IS NULL, 0, _w.client_id) and _owner.id IS NOT NULL, 1, 0) as green_report'),
                DB::raw('IF(_owner.client_id <> IF(_w.client_id IS NULL, 0, _w.client_id)  and _owner.id IS NOT NULL, 1, 0) as yellow_report'),
               
                DB::raw('IF(_purchase.id, 1, 0) as factoring_count'),
                DB::raw('IF(_purchase.id, _purchase.cost, 0) as factoring_sum'),
                DB::raw('IF(_purchase.id, sum(_cd_cost.price), 0) as factoring_detailing'),
                DB::raw('IF(_purchase.id, IF(_collector.id IS NOT NULL, 1, 0), 0) as factoring_collector'),
               
                DB::raw('IF(_logistic.id, 1, 0) as ransom_date'),
                DB::raw('IF(_logistic.id, _purchase.cost, 0) as ransom_sum'),
                DB::raw('IF(_logistic.id, sum(_cd_cost.price), 0) as ransom_detailing'),
                DB::raw('IF(_logistic.id, IF(_collector.id IS NOT NULL, 1, 0), 0) as ransom_collector'),
            )
            ->leftJoin('cars as _car', '_car.id', 'wsm_reserve_new_cars.car_id')
            ->leftJoin('car_full_prices as _full_price', '_full_price.car_id', '_car.id')
            // ->leftJoin('wsm_reserve_new_cars as _reserve', function($join){
            //     $join->on('_reserve.car_id', 'cars.id')
            //         ->whereNull('_reserve.deleted_at');
            // })
            ->leftJoin('wsm_reserve_new_car_contracts as _contract', '_contract.reserve_id', 'wsm_reserve_new_cars.id')//контракт резерва
            ->leftJoin('wsm_reserve_complectation_prices as _wrcp','_wrcp.contract_id', '_contract.id')//сохраненая в контракте цена
            ->leftJoin('complectation_prices as _cp', '_cp.id', '_wrcp.complectation_price_id')//цены комплектации
            ->leftJoin('wsm_reserve_option_prices as _wrop', '_wrop.contract_id', '_contract.id')//сохраненные в контракте опции
            
            ->leftJoin(DB::raw('(SELECT sum(option_prices.price) as _sum_option, reserve.car_id as car_id from option_prices
                left join wsm_reserve_option_prices on wsm_reserve_option_prices.option_price_id = option_prices.id 
                left join wsm_reserve_new_car_contracts on wsm_reserve_new_car_contracts.id = wsm_reserve_option_prices.contract_id 
                left join wsm_reserve_new_cars reserve on reserve.id = wsm_reserve_new_car_contracts.reserve_id 
                where reserve.car_id is not null and reserve.deleted_at is not null
                GROUP  BY  reserve.car_id) as _joinOptionPrice'), '_joinOptionPrice.car_id', '_car.id'
            )
            
            ->addSelect([
                '_joinOptionPrice._sum_option as q_sum_option',
                '_cp.price                   as q_comprice', 
                '_cp.id                      as q_comid', 
            ])
            ->leftJoin('worksheets as _w', '_w.id', 'wsm_reserve_new_cars.worksheet_id')
            ->leftJoin('discounts as _disc', function($join){
                $join->on('_disc.worksheet_id', '=', '_w.id')
                    ->on('_disc.modulable_id', 'wsm_reserve_new_cars.id')
                    ->on('_disc.modulable_type', '=', DB::raw('"App\\\Models\\\WsmReserveNewCar"'));
            })
            ->leftJoin('discount_sums as _disc_sum', '_disc_sum.discount_id', '_disc.id')
            ->leftJoin('car_owners as _owner', '_owner.car_id', '_car.id')
            ->leftJoin('car_purchases as _purchase', '_purchase.car_id', '_car.id')
            ->leftJoin('car_detailing_costs as _cd_cost', '_cd_cost.car_id', '_car.id')
            ->leftJoin('car_collectors as _collector', '_collector.car_id', '_car.id')
            ->leftJoin('car_date_logistics as _logistic', function($join){
                $join->on('_logistic.car_id', '_car.id')
                    ->where('_logistic.logistic_system_name', 'ransom_date');
            });

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);
        
        $countCars = DB::table($query)->select(
            DB::raw('count(id)              as count'),
            DB::raw('sum(t_price)           as tuning'),
            DB::raw('sum(ov_price)          as overprice'),
            DB::raw('sum(com_price)         as base'),
            DB::raw('sum(op_price)          as option'),
            DB::raw('sum(discount_price)    as discount'),
            DB::raw('sum(gift_price)        as giftprice'),

            DB::raw('sum(_disable)          as disable'),
            DB::raw('count(owner_count)     as owner'),
            DB::raw('sum(green_report)      as green'),
            DB::raw('sum(yellow_report)     as yellow'),

            DB::raw('sum(factoring_sum)         as factoring_sum'),
            DB::raw('sum(factoring_count)       as factoring_count'),
            DB::raw('sum(factoring_detailing)   as factoring_detailing'),
            DB::raw('sum(factoring_collector)   as factoring_collector'),

            DB::raw('sum(ransom_date)       as ransom_count'),
            DB::raw('sum(ransom_sum)        as ransom_sum'),
            DB::raw('sum(ransom_detailing)  as ransom_detailing'),
            DB::raw('sum(ransom_collector)  as ransom_collector'),
        )->first();
        
        return $countCars;
    }



    /**
     * ПОЛУЧИТЬ ВСЕ РЕЗЕРВЫ РЛ
     */
    public function getReservesInWorksheet(int $worksheetId): \Illuminate\Database\Eloquent\Collection
    {
        $reserves = WsmReserveNewCar::query()
            ->with(['author', 'contract', 'car', 'payments', 'sales'])
            ->where('worksheet_id', $worksheetId)
            ->withTrashed()
            ->get();

        return $reserves;
    }
}
