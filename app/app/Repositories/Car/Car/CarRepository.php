<?php

namespace App\Repositories\Car\Car;

use App\Classes\Notice\Notice;
use App\Models\Car;
use App\Http\Filters\CarFilter;
use App\Models\CarState;
use App\Repositories\Car\Car\DTO\CarDTO;
use App\Repositories\Car\Car\DTO\CarTuningDTO;
use App\Repositories\Car\Car\DTO\LogisticDateDTO;
use App\Services\Car\CarLogisticStateService;
use DB;

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
        $car->saveTuning((new CarTuningDTO($data['tuning'] ?? [])));

        //Save comment
        $car->saveComment($data['comment'] ?? '');

        //Save collector
        $car->saveCollector($data['collector_id'] ?? null);
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



    public function saveApplication(Car $car)
    {
        $car->logistic_dates()->create([
            'car_id' => $car->id,
            'author_id' => auth()->user()->id,
            'logistic_system_name' => 'application_date',
            'date_at' => '2024-02-02',
        ]);
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

                Notice::setMessage('Автомобиль добавлен.');

                return $car;
            }, 3);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $result;
    }



    /**
     * UPDATE
     */
    public function update(Car $car, array $data)
    {
        try {
            \DB::transaction(function () use ($car, $data) {
                $car->fill((new CarDTO($data))->get())->save();

                $this->saveRelationFasade($car, $data);

                $car->load('logistic_dates');

                $this->setCarStatus($car);

                $car->refresh();

                Notice::setMessage('Автомобиль изменен.');
            }, 3);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }



    /**
     * PAGINATE
     */
    public function paginate(array $data = [], $paginate = 20)
    {
        $query = Car::query()->select('cars.*');

        $query->with([
            'brand',
            'mark',
            'complectation.motor',
            'color',
            'order',
            'provider',
            'author',
            'marker',
            'trade_marker',
            'order_type',
            'logistic_dates',
            'technic',
            'purchase',
            'delivery_terms',
            'detailing_costs',
            'tuning_price',
            'over_price',
            'state_status',
            'complectation' => function ($builderComplectation) {
                $builderComplectation->with(['motor' => function ($builderMotor) {
                    $builderMotor->with(['transmission', 'driver']);
                }]);
            },

            'reserve' => function ($builderReserve) {
                $builderReserve->with(['contract', 'worksheet']);
            },
        ]);

        $filter = app()->make(CarFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter)->orderBy('id', 'DESC');

        $cars = $query->simplePaginate($paginate);

        return $cars;
    }



    /**
     * COUNT CARS WITH FILTER
     * @param array $data FILTER DATA
     * @return int $countCars
     */
    public function count(array $data = []): int
    {
        $query = Car::query()->select('cars.id');

        $filter = app()->make(CarFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $countCars = \DB::table($query)->count();

        return $countCars;
    }
}
