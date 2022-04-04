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

    protected function getCallbacks(): array
    {
        return [
            self::BRAND_ID          => [$this, 'brandId'],
            self::VIN               => [$this, 'vin'],
            self::COMPLECTATION_ID  => [$this, 'complectationId'],
            self::MARK_ID           => [$this, 'markId'],
            self::ARCHIIVE          => [$this, 'archive'],
        ];
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
        $builder->where('car.vin', 'like', '%'. $value.'%');
    }

    public function archive(Builder $builder, $value)
    {
        if($value == 1)
            $builder->onlyTrashed()->with('fixedprice');
    }
}
