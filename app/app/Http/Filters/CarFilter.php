<?php

namespace App\Http\Filters;

use App\Models\CarState;
use App\Models\CarStatusType;
use Illuminate\Database\Eloquent\Builder;

class CarFilter extends AbstractFilter
{
    public const BRAND_ID = 'brand_id';
    public const MARK_ID = 'mark_ids';
    public const YEAR = 'year';
    public const VIN = 'vin';
    public const COMPLECTATION_CODE = 'complectation_code';
    public const COMPLECTATION_ID = 'complectation_id';
    public const BODY = 'bodywork';
    public const TRANSMISSION = 'motor_transmission_id';
    public const TRADE_MARKER_ID = 'trade_marker_id';
    public const MARKER_ID = 'marker_id';
    public const ORDER_NUMBER = 'order_number';
    public const STATE_STATUS = 'statuses';
    public const SEARCH = 'search';
    public const IDS = 'ids';
    public const INIT = 'init';
    public const TYPE_STATUS = 'type_status';



    protected function getCallbacks(): array
    {
        return [
            self::INIT                  => [$this, 'init'],
            self::BRAND_ID              => [$this, 'brandId'],
            self::MARK_ID               => [$this, 'markId'],
            self::YEAR                  => [$this, 'year'],
            self::VIN                   => [$this, 'vin'],
            self::COMPLECTATION_CODE    => [$this, 'complectationCode'],
            self::COMPLECTATION_ID      => [$this, 'complectationId'],
            self::BODY                  => [$this, 'bodyWork'],
            self::TRANSMISSION          => [$this, 'transmission'],
            self::TRADE_MARKER_ID       => [$this, 'tradeMarkerId'],
            self::MARKER_ID             => [$this, 'markerId'],
            self::ORDER_NUMBER          => [$this, 'orderNumber'],
            self::STATE_STATUS          => [$this, 'stateStatus'],
            self::SEARCH                => [$this, 'search'],
            self::IDS                   => [$this, 'ids'],
            self::TYPE_STATUS           => [$this, 'typeStatus'],
        ];
    }



    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder
            ->leftJoin('brands', 'brands.id', 'cars.brand_id') //Бренд
            ->leftJoin('marks', 'marks.id', 'cars.mark_id') //Модель
            ->leftJoin('complectations', 'complectations.id', 'cars.complectation_id') //Комплектация
            ->leftJoin('motors', 'motors.id', 'complectations.motor_id') //Мотор
            ->leftJoin('car_trade_markers', 'car_trade_markers.car_id', 'cars.id') //Товарный признак
            ->leftJoin('car_markers', 'car_markers.car_id', 'cars.id') //Контрмарка
            ->leftJoin('car_orders', 'car_orders.car_id', 'cars.id') //Заказ
            ->leftJoin('car_status_types', 'car_status_types.car_id', 'cars.id') //car type status
            ->groupBy('cars.id')
            ->groupBy('car_trade_markers.id')
            ->groupBy('car_markers.id')
            ->groupBy('car_orders.id');
    }



    /**
     * Тип статуса автомобиля (свободный, резерв, клиент, Продан)
     */
    public function typeStatus(Builder $builder, array $value)
    {
        $arr = array_unique($value);

        $result = array_intersect($arr, CarStatusType::VALUES);

        $builder->whereIn('car_status_types.status', $result);
    }



    /**
     * Выбранные ИД
     */
    public function ids(Builder $builder, array $value)
    {
        $builder->whereIn('cars.id', $value);
    }



    /**
     * Поиск по Вин, ИД, НомерЗаказа
     */
    public function search(Builder $builder, $value)
    {
        $builder->where(function ($query) use ($value) {
            $query->where('cars.vin', 'LIKE', '%' . $value . '%')
                ->orWhere('cars.id', 'LIKE', '%' . $value . '%')
                ->orWhere('car_orders.order_number',  'LIKE', '%' . $value . '%');
        });
    }



    /**
     * Логистический статус (В заказе, в отгрузке и тд)
     */
    public function stateStatus(Builder $builder, array $value)
    {
        $baseState = CarState::pluck('status')->toArray();

        $arr = array_intersect($baseState, $value);

        $builder->whereIn('cars.status', $arr);
    }



    /**
     * НомерЗаказа
     */
    public function orderNumber(Builder $builder, $value)
    {
        $builder->where('car_orders.order_number', 'LIKE', '%' . $value . '%');
    }



    /**
     * Товарный признак
     */
    public function tradeMarkerId(Builder $builder, $value)
    {
        $builder->where('car_trade_markers.trade_marker_id', $value);
    }



    /**
     * КонтрМарка
     */
    public function markerId(Builder $builder, $value)
    {
        $builder->where('car_markers.marker_id', $value);
    }



    /**
     * Бренд
     */
    public function brandId(Builder $builder, $value)
    {
        $builder->where('cars.brand_id',  $value);
    }



    /**
     * Модель
     */
    public function markId(Builder $builder, array $value)
    {
        $builder->whereIn('cars.mark_id',  $value);
    }



    /**
     * Год выпуска
     */
    public function year(Builder $builder, $value)
    {
        $builder->where('cars.year', $value);
    }



    /**
     * ВИН
     */
    public function vin(Builder $builder, $value)
    {
        $builder->where('cars.vin', 'LIKE', '%' . $value . '%');
    }



    /**
     * Код комплектации
     */
    public function complectationCode(Builder $builder, $value)
    {
        $builder->where('complectations.code', $value);
    }



    /**
     * ИД Комплектации
     */
    public function complectationId(Builder $builder, $value)
    {
        $builder->where('complectations.id', $value);
    }



    /**
     * Тип кузова
     */
    public function bodyWork(Builder $builder, $value)
    {
        $builder->where('complectations.body_work_id', $value);
    }



    /**
     * Трансмиссия
     */
    public function transmission(Builder $builder, $value)
    {
        $builder->where('motors.motor_transmission_id', $value);
    }
}
