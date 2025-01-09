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
                'cars.id', 
                'cfp.tuningprice as t_price', 
                'cfp.overprice as ov_price',
                'cfp.giftprice as gift_price',
                DB::raw('IF(cp.price IS NOT NULL, cp.price, cfp.complectationprice) as com_price'),
                DB::raw('IF(joinOptionPrice.sum_option IS NOT NULL, joinOptionPrice.sum_option, cfp.optionprice) as op_price'),
                DB::raw('(_joinds._dsum) as discount_price'),

                'car_owners.id as owner_count',
                DB::raw('IF(cars.disable_off, cars.disable_off, 0) as _disable'),
                DB::raw('IF(car_owners.client_id = IF(worksheets.client_id IS NULL, 0, worksheets.client_id) and car_owners.id IS NOT NULL, 1, 0) as green_report'),
                DB::raw('IF(car_owners.client_id <> IF(worksheets.client_id IS NULL, 0, worksheets.client_id)  and car_owners.id IS NOT NULL, 1, 0) as yellow_report'),
               
                DB::raw('IF(ransom_cars.car_id, 1, 0) as ransom_date'),
                DB::raw('IF(ransom_cars.car_id, _purchase.cost, 0) as ransom_sum'),
                DB::raw('IF(ransom_cars.car_id, sum(car_detailing_costs.price), 0) as ransom_detailing'),
                DB::raw('IF(ransom_cars.car_id, IF(car_collectors.id IS NOT NULL, 1, 0), 0) as ransom_collector'),
               
                DB::raw('IF(_purchase.id, 1, 0) as factoring_count'),
                DB::raw('IF(_purchase.id, _purchase.cost, 0) as factoring_sum'),
                DB::raw('IF(_purchase.id, sum(car_detailing_costs.price), 0) as factoring_detailing'),
                DB::raw('IF(_purchase.id, IF(car_collectors.id IS NOT NULL, 1, 0), 0) as factoring_collector'),
            );

        $query->leftJoin(DB::raw('(
                SELECT sum(_ds.amount) as _dsum, _d.modulable_id as _dreserve FROM discounts as _d 
                LEFT JOIN discount_sums as _ds on _ds.discount_id = _d.id group by _d.modulable_id
            ) as _joinds'), '_joinds._dreserve', 'wsm_reserve_new_cars.id');

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);

        $query
            ->leftJoin('car_collectors', 'car_collectors.car_id', 'cars.id')
            ->leftJoin('car_purchases as _purchase', '_purchase.car_id', 'cars.id');
        
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
