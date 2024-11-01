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

    public const INIT = 'init';

    protected function getCallbacks(): array
    {
        return [
            self::INIT              => [$this, 'init'],
            self::BRAND_ID          => [$this, 'brandId'],
            self::NAME              => [$this, 'name'],
            self::CODE              => [$this, 'code'],
            self::MARK_ID           => [$this, 'markId'],
        ];
    }



    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder->leftJoin('marks', 'marks.id', 'complectations.mark_id');
    }



    public function brandId(Builder $builder, $value)
    {
        $builder->where('marks.brand_id',  $value);
    }



    public function markId(Builder $builder, $value)
    {
        $builder->where('marks.id',  $value);
    }



    public function code(Builder $builder, $value)
    {
        $builder->where('complectations.code', 'like', '%'. $value.'%');
    }



    public function name(Builder $builder, $value)
    {
        $builder->where('complectations.name', 'like', '%'. $value.'%');
    }
}
