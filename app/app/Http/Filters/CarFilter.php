<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class CarFilter extends AbstractFilter
{
    public const BRAND_ID = 'brand_id';
    public const VIN = 'vin';
    public const COMPLECTATION_ID = 'complectation_id';
    public const MARK_ID = 'mark_id';
    public const ARCHIIVE = 'archive';
    public const DELIVERY_TYPE_ID = 'delivery_type_id';
    public const DELIVERY_STAGE_ID = 'delivery_stage_id';
    public const REVALUATION = 'revaluation';

    protected function getCallbacks(): array
    {
        return [
            self::BRAND_ID          => [$this, 'brandId'],
            self::VIN               => [$this, 'vin'],
            self::COMPLECTATION_ID  => [$this, 'complectationId'],
            self::MARK_ID           => [$this, 'markId'],
            self::ARCHIIVE          => [$this, 'archive'],
            self::DELIVERY_TYPE_ID  => [$this, 'deliveryTypeId'],
            self::DELIVERY_STAGE_ID => [$this, 'deliveryStageId'],
            self::REVALUATION       => [$this, 'revaluation'],
        ];
    }

    public function revaluation(Builder $builder, $value)
    {
        $builder->leftJoin('complectations', 'complectations.id', 'cars.complectation_id')
            ->where('complectations.price_status', 1);
    }

    public function deliveryTypeId(Builder $builder, $value)
    {
        $builder->where('car_deliveries.delivery_type_id', $value);
    }

    public function deliveryStageId(Builder $builder, $value)
    {
        $builder->where('car_deliveries.delivery_stage_id', $value);
    }

    public function brandId(Builder $builder, $value)
    {
        $builder->where('cars.brand_id',  $value);
    }

    public function markId(Builder $builder, $value)
    {
        $builder->where('cars.mark_id',  $value);
    }

    public function complectationId(Builder $builder, $value)
    {
        $builder->where('cars.complectation_id',  $value);
    }

    public function vin(Builder $builder, $value)
    {
        $builder->where('cars.vin', 'like', '%'. $value.'%');
    }

    public function archive(Builder $builder, $value)
    {
        if($value == 1)
            $builder->onlyTrashed()->with('fixedprice');
    }
}
