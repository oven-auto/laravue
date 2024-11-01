<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Exceptions\Reserve\ReserveException;
use App\Http\Filters\ReserveNewCarFilter;
use App\Models\Car;
use App\Models\WsmReserveNewCar;
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
            if($reserve->hasPDKP() && $reserve->hasDKP() && !$newCar->isInvoice())
            {
                $reserve->contract->fill([
                    'dkp_offer_at' => null,
                    'dkp_decorator_id' => null,
                ])->save();
            }
            elseif(!$reserve->hasPDKP() && $reserve->hasDKP() && !$newCar->isInvoice())
            {
                $reserve->contract->fill([
                    'dkp_offer_at' => null,
                    'dkp_decorator_id' => null,
                    'pdkp_offer_at' => $reserve->contract->dkp_offer_at,
                    'pdkp_decorator_id' => $reserve->contract->dkp_decorator_id
                ])->save();
            }

            $reserve->fill(['car_id' => $newCar->id])->save();
            
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

        $query->withDataForReserveList();

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $reserves = $query->groupBy('wsm_reserve_new_cars.id')->orderBy('wsm_reserve_new_cars.id', 'DESC')->simplePaginate($paginate);

        $reserves->each(function($item) {
            $item->car->reserve = $item;
        });
        
        return $reserves;
    }



    public function counter(array $data): int
    {
        $query = WsmReserveNewCar::query();

        $subQuery = WsmReserveNewCar::query()->select('wsm_reserve_new_cars.*');

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => array_filter($data)]);

        $subQuery->filter($filter);

        $query->rightJoinSub($subQuery, 'subQuery', function ($join) {
            $join->on('subQuery.id', '=', 'wsm_reserve_new_cars.id');
        });

        $result = $query->count();

        return $result;
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
