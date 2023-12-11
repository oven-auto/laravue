<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

Class WorksheetAnalyticFilter extends AbstractFilter
{
    public const INIT = 'init';
    public const INTERVAL_BEGIN = 'interval_begin';
    public const INTERVAL_END = 'interval_end';
    public const APPEAL_IDS = 'appeal_ids';
    public const AUTHOR_ID = 'author_id';
    public const EXECUTOR_ID = 'manager_id';
    public const CREATED_BEGIN = 'created_begin';
    public const CREATED_END = 'created_end';
    public const CLOSED_BEGIN = 'closed_begin';
    public const CLOSED_END = 'closed_end';
    public const COMPANY_IDS = 'company_ids';
    public const STRUCTURE_IDS = 'structure_ids';

    public function __construct($queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }

    protected function getCallbacks() : array
    {
        return [
            self::INIT                  => [$this, 'init'],
            self::INTERVAL_BEGIN        => [$this,'intervalBegin'],
            self::INTERVAL_END          => [$this,'intervalEnd'],
            self::APPEAL_IDS            => [$this,'appealIds'],
            self::EXECUTOR_ID           => [$this,'executorIds'],
            self::AUTHOR_ID             => [$this,'authorId'],

            self::CREATED_BEGIN         => [$this,'createdBegin'],
            self::CREATED_END           => [$this,'createdEnd'],

            self::CLOSED_BEGIN          => [$this,'closedBegin'],
            self::CLOSED_END            => [$this,'closedEnd'],

            self::STRUCTURE_IDS         => [$this, 'structureIds'],
            self::COMPANY_IDS           => [$this, 'companyIds'],
        ];
    }

    public function init(Builder $builder)
    {
        $builder->leftJoin('worksheet_actions', function($join) {
                $join->on('worksheet_actions.worksheet_id','worksheets.id');
            })
            // ->where(
            //     'worksheet_actions.id',
            //     \DB::raw('(SELECT max(SWA.id) FROM worksheet_actions as SWA WHERE SWA.worksheet_id = worksheets.id)')
            // )
            ->leftJoin('trafics', 'trafics.id', 'worksheets.trafic_id')
            ->where('trafics.client_type_id', '<>', 3);
    }

    public function createdBegin(Builder $builder, $value)
    {
        $builder->whereDate('worksheets.created_at', '>=', $this->formatDate($value));
    }

    public function createdEnd(Builder $builder, $value)
    {
        $builder->whereDate('worksheets.created_at', '<=', $this->formatDate($value));
    }

    public function closedBegin(Builder $builder, $value)
    {
        $builder->whereIn('worksheet_actions.status', ['confirm','abort'])
            ->whereDate('worksheet_actions.updated_at', '>=', $this->formatDate($value));
    }

    public function closedEnd(Builder $builder, $value)
    {
        $builder->whereDate('worksheet_actions.updated_at', '<=', $this->formatDate($value));
    }

    public function intervalBegin(Builder $builder, $value)
    {

    }

    public function intervalEnd(Builder $builder, $value)
    {

    }

    public function appealIds(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->whereIn('worksheets.appeal_id', $value);
        elseif(is_numeric($value) || is_string($value))
            $builder->where('worksheets.appeal_id', $value);
    }

    public function authorId(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->whereIn('worksheets.author_id', $value);
        elseif(is_numeric($value) || is_string($value))
            $builder->where('worksheets.author_id', $value);
    }

    public function executorIds(Builder $builder, $value)
    {
        $builder->leftJoin('worksheet_executors','worksheet_executors.worksheet_id','worksheets.id');

        if(is_array($value))
            $builder->whereIn('worksheet_executors.user_id', $value);
        elseif(is_numeric($value) || is_string($value))
            $builder->where('worksheet_executors.user_id', $value);
    }

    public function companyIds(Builder $builder, $value)
    {
        $builder->whereIn('worksheets.company_id', $value);
    }

    public function structureIds(Builder $builder, $value)
    {
        $builder->leftJoin('company_structures', 'company_structures.structure_id', 'worksheets.structure_id');

        $builder->whereIn('company_structures.id', $value);
    }
}
