<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class CarFilter extends AbstractFilter
{
    public const BRAND_ID = 'brand_id';
    public const MARK_ID = 'mark_id';
    public const YEAR = 'year';
    public const VIN = 'vin';
    public const COMPLECTATION_CODE = 'complectation_code';
    public const COMPLECTATION_ID = 'complectation_id';
    public const BODY = 'bodywork';
    public const TRANSMISSION = 'motor_transmission_id';
    public const TRADE_MARKER_ID = 'trade_marker_id';
    public const MARKER_ID = 'marker_id';
    public const ORDER_NUMBER = 'order_number';
    public const STATE_STATUS = 'status';
    public const SEARCH = 'search';
    public const IDS = 'ids';
    public const INIT = 'init';



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
            ->groupBy('cars.id')
            ->groupBy('car_trade_markers.id')
            ->groupBy('car_markers.id')
            ->groupBy('car_orders.id');
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
            $query->where('cars.vin', $value)
                ->orWhere('cars.id', $value)
                ->orWhere('car_orders.order_number', $value);
        });
    }



    /**
     * Логистический статус
     */
    public function stateStatus(Builder $builder, $value)
    {
        $builder->where('cars.status', $value);
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
    public function markId(Builder $builder, $value)
    {
        $builder->where('cars.mark_id',  $value);
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
