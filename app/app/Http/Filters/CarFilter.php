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
            ->leftJoin('brands', 'brands.id', 'cars.brand_id')
            ->leftJoin('marks', 'marks.id', 'cars.mark_id')
            ->leftJoin('complectations', 'complectations.id', 'cars.complectation_id')
            ->leftJoin('motors', 'motors.id', 'complectations.motor_id')
            ->leftJoin('car_trade_markers', 'car_trade_markers.car_id', 'cars.id')
            ->leftJoin('car_markers', 'car_markers.car_id', 'cars.id')
            ->leftJoin('car_orders', 'car_orders.car_id', 'cars.id')
            ->groupBy('cars.id')
            ->groupBy('car_trade_markers.id')
            ->groupBy('car_markers.id')
            ->groupBy('car_orders.id');
    }



    public function search(Builder $builder, $value)
    {
        $builder->where(function ($query) use ($value) {
            $query->where('cars.vin', $value)
                ->orWhere('cars.id', $value)
                ->orWhere('car_orders.order_number', $value);
        });
    }



    /**
     * STATE STATUS
     */
    public function stateStatus(Builder $builder, $value)
    {
        $builder->where('cars.status', $value);
    }



    /**
     * ORDER NUMBER
     */
    public function orderNumber(Builder $builder, $value)
    {
        $builder->where('car_orders.order_number', 'LIKE', '%' . $value . '%');
    }



    /**
     * TRADE MARKER
     */
    public function tradeMarkerId(Builder $builder, $value)
    {
        $builder->where('car_trade_markers.trade_marker_id', $value);
    }



    /**
     * LOGIST MARKER
     */
    public function markerId(Builder $builder, $value)
    {
        $builder->where('car_markers.marker_id', $value);
    }



    /**
     * BRAND
     */
    public function brandId(Builder $builder, $value)
    {
        $builder->where('cars.brand_id',  $value);
    }



    /**
     * MARK
     */
    public function markId(Builder $builder, $value)
    {
        $builder->where('cars.mark_id',  $value);
    }



    /**
     * YEAR
     */
    public function year(Builder $builder, $value)
    {
        $builder->where('cars.year', $value);
    }



    /**
     * VIN
     */
    public function vin(Builder $builder, $value)
    {
        $builder->where('cars.vin', 'LIKE', '%' . $value . '%');
    }



    /**
     * COMPLECTATION CODE
     */
    public function complectationCode(Builder $builder, $value)
    {
        $builder->where('complectations.code', $value);
    }



    /**
     * COMPLECTATION ID
     */
    public function complectationId(Builder $builder, $value)
    {
        $builder->where('complectations.id', $value);
    }



    /**
     * BODYWORK
     */
    public function bodyWork(Builder $builder, $value)
    {
        $builder->where('complectations.body_work_id', $value);
    }



    /**
     * TRANSMISSION
     */
    public function transmission(Builder $builder, $value)
    {
        $builder->where('motors.motor_transmission_id', $value);
    }
}
