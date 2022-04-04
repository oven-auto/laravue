<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ComplectationFilter extends AbstractFilter
{
    public const BRAND_ID = 'brand_id';
    public const NAME = 'name';
    public const CODE = 'code';
    public const MARK_ID = 'mark_id';
    public const STATUS = 'status';

    protected function getCallbacks(): array
    {
        return [
            self::BRAND_ID          => [$this, 'brandId'],
            self::NAME              => [$this, 'name'],
            self::CODE              => [$this, 'code'],
            self::MARK_ID           => [$this, 'markId'],
            self::STATUS            => [$this, 'status'],
        ];
    }

    public function brandId(Builder $builder, $value)
    {
        $builder->where('complectations.brand_id',  $value);
    }

    public function code(Builder $builder, $value)
    {
        $builder->where('complectations.code', 'like', '%'. $value.'%');
    }

    public function name(Builder $builder, $value)
    {
        $builder->where('complectations.name', 'like', '%'. $value.'%');
    }

    public function markId(Builder $builder, $value)
    {
        $builder->where('complectations.mark_id', $value);
    }

    public function status(Builder $builder, $value)
    {
        $builder->where('complectations.status', 1);
    }
}
