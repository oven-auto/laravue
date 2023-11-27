<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

Class TraficAnalyticFilter extends AbstractFilter
{
    public const INTERVAL_BEGIN = 'interval_begin';
    public const INTERVAL_END = 'interval_end';
    public const APPEAL_IDS = 'appeal_ids';
    public const AUTHOR_ID = 'author_id';
    public const MANAGER_ID = 'manager_id';

    /*-----------------------------------------*/
    public const INIT = 'init';

    protected function getCallbacks() : array
    {
        return [
            self::INIT              => [$this, 'init'],
            self::INTERVAL_BEGIN    => [$this, 'intervalBegin'],
            self::INTERVAL_END      => [$this, 'intervalEnd'],
            self::APPEAL_IDS        => [$this, 'appealIds'],
            self::MANAGER_ID        => [$this, 'managerId'],
            self::AUTHOR_ID         => [$this, 'authorId'],
        ];
    }

    public function __construct($queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }

    public function init(Builder $builder)
    {
        $builder->leftJoin('trafic_appeals', 'trafic_appeals.id', 'trafics.trafic_appeal_id');
    }

    public function intervalBegin(Builder $builder, $value) : void
    {
        $carbon = Carbon::createFromFormat('Y-m-d', $this->formatDate($value));

        $builder->whereDate('trafics.created_at', '>=', $carbon);
    }

    public function intervalEnd(Builder $builder, $value) : void
    {
        $carbon = Carbon::createFromFormat('Y-m-d', $this->formatDate($value));

        $builder->whereDate('trafics.created_at', '<=', $carbon);
    }

    public function appealIds(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->whereIn('trafic_appeals.appeal_id', $value);
        elseif(is_numeric($value))
            $builder->where('trafic_appeals.appeal_id', $value);
    }

    public function managerId(Builder $builder, $value)
    {
        if(is_array($value))
        {
            $builder->where(function($query) use ($value){
                $query->whereIn('trafics.manager_id', $value)
                    ->orWhereNull('trafics.manager_id');
            });

        }
        elseif(is_numeric($value))
            $builder->where('trafics.manager_id', $value);
    }

    public function authorId(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->whereIn('trafics.author_id', $value);
        elseif(is_numeric($value))
            $builder->where('trafics.author_id', $value);

    }
}
