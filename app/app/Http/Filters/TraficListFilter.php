<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;

Class TraficListFilter extends AbstractFilter{

    public const SHOW = 'show';
    public const CONTROL_DATE = 'control_date';
    public const MANAGER_ID = 'manager_id';
    public const DATE_FOR_CLOSING = 'date_for_closing';
    public const OPENING = [1,2,6];
    public const CLOSING = [3,4,5];

    protected function getCallbacks(): array
    {
        return [
            self::SHOW              => [$this, 'show'],
            self::CONTROL_DATE      => [$this, 'controlDate'],
            self::MANAGER_ID        => [$this, 'managerId'],
            self::DATE_FOR_CLOSING  => [$this, 'dateForClosing'],
        ];
    }

    public function show(Builder $builder, $value)
    {
        switch ($value) {
            case 'closing':
                $builder->whereIn('trafics.trafic_status_id', self::CLOSING);
                break;
            case 'opening':
                $builder->whereIn('trafics.trafic_status_id', self::OPENING);
                break;
            default:
                $builder->whereIn('trafics.trafic_status_id', self::OPENING);
        }
    }

    public function controlDate(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        if(now()->format('Y-m-d') >= $date)
            $builder->where(function($query) use ($date){
                $query->whereDate('trafics.begin_at', '<=', $date)->orWhereNull('trafics.begin_at');
            });
        else
            $builder->whereDate('trafics.begin_at', '=', $date);
    }

    public function managerId(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->where(function($query) use ($value) {
                $query
                    ->whereIn('trafics.manager_id', $value)
                    ->orWhereIn('trafics.author_id', $value);
            });
        else
            $builder->where(function($query) use ($value){
                $query->where('trafics.manager_id', $value)
                    ->orWhere('trafics.author_id', $value);
            });
    }

    public function dateForClosing(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        $builder->whereDate('trafics.begin_at', '=', $date);
    }
}
