<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Http\Filters\ReserveNewCarFilter;
use App\Models\WsmReserveNewCar;
use Exception;

class ReserveRepository
{
    public function isFreeCar(int $carId)
    {
        $car = WsmReserveNewCar::where('car_id', $carId)->first();

        if ($car)
            return 0;
        return 1;
    }



    public function saveDealDate(WsmReserveNewCar $reserve, array $data)
    {
        $type = $data['type'];
        $arrObj = ['reserve_id' => $reserve->id];
        $arrData = [
            'decorator_id'  => $data['decorator_id'],
            'date_at'       => $data['date_at'],
            'author_id'     => auth()->user()->id,
        ];

        switch ($type) {
            case 'sale':
                $reserve->sale()->updateOrCreate($arrObj, $arrData);
                break;
            case 'issue':
                $reserve->issue()->updateOrCreate($arrObj, $arrData);
                break;
            default:
                throw new \Exception('Ошибка при выполнении');
        }
    }



    /**
     * ПОЛУЧИТЬ ВСЕ РЕЗЕРВЫ РЛ
     */
    public function getReservesInWorksheet(int $worksheetId): \Illuminate\Database\Eloquent\Collection
    {
        $reserves = WsmReserveNewCar::query()
            ->with(['author', 'contract', 'car', 'payments', 'sales'])
            ->where('worksheet_id', $worksheetId)
            ->get();

        return $reserves;
    }



    /**
     * СОЗДАТЬ НОВЫЙ РЕЗЕРВ
     */
    public function createReserve(array $data): WsmReserveNewCar
    {
        if (!$this->isFreeCar($data['car_id']))
            throw new Exception('Этот автомобиль уже зарезервирован под другого клиента.');

        $reserve = WsmReserveNewCar::create(array_merge(
            $data,
            ['author_id' => auth()->user()->id]
        ));

        return $reserve;
    }



    /**
     * ЗАМЕНИТЬ АВТОМОБИЛЬ В РЕЗЕРВЕ
     */
    public function changeCarInReserve(WsmReserveNewCar $reserve, array $data): void
    {
        if (!$this->isFreeCar($data['car_id']))
            throw new Exception('Этот автомобиль уже зарезервирован под другого клиента.');

        $reserve->car_id = $data['car_id'];

        $reserve->save();
    }



    /**
     * УДАЛИТЬ РЕЗЕРВ
     */
    public function deleteReserve(WsmReserveNewCar $reserve): void
    {
        if ($reserve->contract->isWorking())
            throw new Exception('Имеются открытые договоры.');

        $reserve->delete();
    }



    public function attachTradeIn(WsmReserveNewCar $reserve, array $data)
    {
        $reserve->tradeins()->sync($data);
    }



    public function paginate(array $data, $paginate = 20)
    {
        $query = WsmReserveNewCar::select('wsm_reserve_new_cars.*');

        $query->with([
            'author', 'contract', 'sales', 'payments', 'issue', 'sale',
            'worksheet' => function ($builderWorksheet) {
                $builderWorksheet->with(['executors', 'client.phones', 'redemptions.client_car' => function ($builderRedemption) {
                    $builderRedemption->with('mark', 'brand');
                }]);
            },
            'car' => function ($builderCar) {
                $builderCar->with([
                    'brand', 'mark', 'complectation.motor', 'color', 'order', 'provider', 'author',
                    'marker', 'trade_marker', 'order_type', 'logistic_dates', 'technic', 'purchase',
                    'delivery_terms', 'detailing_costs', 'tuning_price', 'over_price', 'state_status',
                    'complectation' => function ($builderComplectation) {
                        $builderComplectation->with(['motor' => function ($builderMotor) {
                            $builderMotor->with(['transmission', 'driver']);
                        }]);
                    }
                ]);
            },
            'tradeins' => function ($builderUsedCar) {
                $builderUsedCar->with(['brand', 'mark']);
            },
        ]);

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $reserves = $query->groupBy('wsm_reserve_new_cars.id')->simplePaginate($paginate);

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
}
