<?php

namespace App\Repositories\Car\Car;

use App\Classes\Notice\Notice;
use App\Classes\Wait\Wait;
use App\Models\Car;
use App\Http\Filters\CarFilter;
use App\Models\CarState;
use App\Models\DealerColorImage;
use App\Repositories\Car\Car\DTO\CarCountDTO;
use App\Repositories\Car\Car\DTO\CarDTO;
use App\Repositories\Car\Car\DTO\CarTuningDTO;
use App\Repositories\Car\Car\DTO\LogisticDateDTO;
use App\Services\Car\CarLogisticStateService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CarRepository
{
    /**
     * SAVE RELATION
     */
    public function saveRelationFasade(Car $car, array $data)
    {
        //Создаем если не было создано, либо изменяем НОМЕР ЗАКАЗА
        $car->saveOrderNumber($data['order_number'] ?? '');

        //сохраняем маркер логиста, так как поле не обязательное может быть пустота
        $car->saveMarker($data['marker_id'] ?? null);

        //Сохраняем товарный признак авто
        $car->saveTradeMarker($data['trade_marker_id'] ?? null);

        //Сохраняем поставщика
        $car->saveProvider($data['provider_id'] ?? null);

        //Сохраняем тип заказа, сохранением занимается метод модели car
        $car->saveOrderType($data['order_type_id'] ?? null);

        //Сохраняем даты логистики
        $car->saveLogisticDates(new LogisticDateDTO($data ?? []));

        //Сохраняем поставщика
        $car->saveTechnic($data['technic_id'] ?? null);

        //Сохраняем аудио
        $car->saveAudio($data['audio_code'] ?? null);

        //Сохраняем закуп
        $car->savePurchase($data['purchase_cost'] ?? null);

        //Сохраняем условия поставки
        $car->saveDeliveryTerm($data['delivery_term_id'] ?? null);

        //Сохраняем опции
        $car->saveOptions($data['options'] ?? null);

        //Сохраняем детализацию цены
        $car->saveDetailingCosts($data['detailing_costs'] ?? null);

        //Сохраняем тюнинг
        $car->saveTuning(($data['devices'] ?? []));

        //Save comment
        $car->saveComment($data['comment'] ?? '');

        //Save collector
        $car->saveCollector($data['collector_id'] ?? null);

        //Save Owner
        $car->saveOwner($data['owner'] ?? null);

        //save gift price
        $car->saveGiftPrice($data['gift_price'] ?? null);

        //save part price
        $car->savePartPrice($data['part_price'] ?? null);

        //save tuning price
        $car->saveTuningPrice($data['tuning_price'] ?? null);
        
        $car->savePaidDate($data['paid_date'] ?? null);

        $car->saveControlPaidDate($data['control_paid_date'] ?? null);
    }



    /**
     * SAVE OVER PRICE
     */
    public function saveOverPrice(Car $car, int $price)
    {
        $car->saveOverPrice($price);
    }



    public function setCarStatus(Car $car)
    {   
        $stateService = new CarLogisticStateService($car);

        $lastState = $stateService->getLastLogisticState();

        if (!$lastState)
            return;

        $carState = CarState::query()->where('logistic_system_name', $lastState->logistic_system_name)->first();

        $car->saveCarStatus($carState);
    }



    public function setCarImage(Car $car)
    {
        

        $bodyWork = $car->complectation->body_work_id;

        $colorImage = $car->color->images->where('body_work_id', $bodyWork)->first();

        if(!$car->color->images->contains('body_work_id', $bodyWork))
            throw new \Exception('Нет картинки для этого цвета.');

        if($colorImage)
            $car->image()->sync([$colorImage->id]);
    }



    /**
     * CREATE
     */
    public function store(array $data)
    {
        try {
            $result = DB::transaction(function () use ($data) {
                $car = Car::create(array_merge((new CarDTO($data))->get(), ['author_id' => auth()->user()->id]));

                $data['application_date'] = $car->created_at->format('d.m.Y');

                $this->saveRelationFasade($car, $data);

                $car->load('logistic_dates');

                $this->setCarStatus($car);

                $car->refresh();

                $this->setCarImage($car);

                Notice::setMessage('Автомобиль добавлен.');

                return $car;
            }, 3);
        } catch (\Exception $exception) {
            throw new \Exception('Произошла ошибка, автомобиль не добавлен.');
        }

        return $result;
    }



    /**
     * UPDATE
     */
    public function update(Car $car, array $data)
    {
        try {
            DB::transaction(function () use ($car, $data) {
                $data['application_date'] = $car->created_at->format('d.m.Y');

                $car->fill((new CarDTO($data))->get())->save();

                $this->saveRelationFasade($car, $data);

                $car->load('logistic_dates');

                $this->setCarStatus($car);

                $car->refresh();

                $this->setCarImage($car);

                Notice::setMessage('Автомобиль изменен.');
            }, 3);
        } catch (\Exception $exception) {
            throw new \Exception('Произошла ошибка, автомобиль не изменен. '.$exception->getMessage());
        }
    }



    /**
     * PAGINATE
     */
    public function paginate(array $data = [], $paginate = 15)
    {   
        $query = Car::query()->select('cars.*');
        
        $filter = app()->make(CarFilter::class, ['queryParams' => $data]);
        
        if(DealerColorImage::select(DB::raw('count(id) as count'))->first()->toArray()['count'] == 0)
            $query->withDataForCarList();

        $query->filter($filter)->orderBy('id', 'DESC');
       
        $cars = $query->simplePaginate($paginate);
        
        if(DealerColorImage::select(DB::raw('count(id) as count'))->first()->toArray()['count'] == 0)
            $cars->each(function($item) {
                if(isset($item->reserve))
                    $item->reserve->car = $item;
            });
        
        return $cars;
    }



    /**
     * COUNT CARS WITH FILTER
     * @param array $data FILTER DATA
     * @return int $countCars
     */
    public function count(array $data = [])
    {
        $query = Car::query()
            ->select(
                'cars.id', 
                '_full_price.tuningprice as t_price', 
                '_full_price.overprice as ov_price',
                '_full_price.giftprice as gift_price',
                DB::raw('IF(_cp.price IS NOT NULL, _cp.price, _full_price.complectationprice) as com_price'),
                DB::raw('IF(_joinOptionPrice._sum_option IS NOT NULL, _joinOptionPrice._sum_option, _full_price.optionprice) as op_price'),
                DB::raw('sum(_disc_sum.amount) as discount_price'),

                //'cars.disable_off as _disable',
                '_owner.id as owner_count',
                DB::raw('IF(cars.disable_off, cars.disable_off, 0) as _disable'),
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
            ->leftJoin('car_full_prices as _full_price', '_full_price.car_id', 'cars.id')
            ->leftJoin('wsm_reserve_new_cars as _reserve', function($join){
                $join->on('_reserve.car_id', 'cars.id')
                    ->whereNull('_reserve.deleted_at');
            })
            ->leftJoin('wsm_reserve_new_car_contracts as _contract', '_contract.reserve_id', '_reserve.id')//контракт резерва
            ->leftJoin('wsm_reserve_complectation_prices as _wrcp','_wrcp.contract_id', '_contract.id')//сохраненая в контракте цена
            ->leftJoin('complectation_prices as _cp', '_cp.id', '_wrcp.complectation_price_id')//цены комплектации
            ->leftJoin('wsm_reserve_option_prices as _wrop', '_wrop.contract_id', '_contract.id')//сохраненные в контракте опции
            ->leftJoin(DB::raw('(SELECT sum(option_prices.price) as _sum_option, wsm_reserve_new_cars.car_id from option_prices
                left join wsm_reserve_option_prices on wsm_reserve_option_prices.option_price_id = option_prices.id 
                left join wsm_reserve_new_car_contracts on wsm_reserve_new_car_contracts.id = wsm_reserve_option_prices.contract_id 
                left join wsm_reserve_new_cars on wsm_reserve_new_cars.id = wsm_reserve_new_car_contracts.reserve_id 
                where wsm_reserve_new_cars.car_id is not null and wsm_reserve_new_cars.deleted_at is not null
                GROUP  BY  wsm_reserve_new_cars.car_id) as _joinOptionPrice'), '_joinOptionPrice.car_id', 'cars.id'
            )
            ->addSelect([
                '_joinOptionPrice._sum_option as q_sum_option',
                '_cp.price                   as q_comprice', 
                '_cp.id                      as q_comid', 
            ])
            ->leftJoin('worksheets as _w', '_w.id', '_reserve.worksheet_id')
            ->leftJoin('discounts as _disc', function($join){
                $join->on('_disc.worksheet_id', '=', '_w.id')
                    ->on('_disc.modulable_id', '_reserve.id')
                    ->on('_disc.modulable_type', '=', DB::raw('"App\\\Models\\\WsmReserveNewCar"'));
            })
            ->leftJoin('discount_sums as _disc_sum', '_disc_sum.discount_id', '_disc.id')
            ->leftJoin('car_owners as _owner', '_owner.car_id', 'cars.id')
            ->leftJoin('car_purchases as _purchase', '_purchase.car_id', 'cars.id')
            ->leftJoin('car_detailing_costs as _cd_cost', '_cd_cost.car_id', 'cars.id')
            ->leftJoin('car_collectors as _collector', '_collector.car_id', 'cars.id')
            ->leftJoin('car_date_logistics as _logistic', function($join){
                $join->on('_logistic.car_id', 'cars.id')
                    ->where('_logistic.logistic_system_name', 'ransom_date');
            });

        $filter = app()->make(CarFilter::class, ['queryParams' => ($data)]);

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



    public function clone(Car $car)
    {
        $clone = new Car();
        
        $clone->fill([
            'year'                      => $car->year,
            'mark_id'                   => $car->mark_id, //model
            'brand_id'                  => $car->brand_id, //brand
            'complectation_id'          => $car->complectation_id,
            'author_id'                 => $car->author_id,
            'status'                    => $car->status,
            'created_at'                => $car->created_at,
        ]);
        
        $clone->trade_marker            = $car->trade_marker;
        $clone->provider                = $car->provider;

        return $clone;
    }
}
