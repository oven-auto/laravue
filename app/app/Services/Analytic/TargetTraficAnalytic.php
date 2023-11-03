<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficFilter;
use App\Models\Trafic;
use App\Models\TraficStatus;
use Carbon\Carbon;

Class TargetTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic($data = [])
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $subQuery = Trafic::select([
                'trafics.trafic_status_id',
                \DB::raw('COUNT(trafics.trafic_status_id) as count'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->withTrashed(),
            ])
            ->withTrashed()
            ->where('trafics.client_type_id', '<>', 3)
            ->filter($filter)
            ->groupBy('trafics.trafic_status_id');

        $query = TraficStatus::select('trafic_statuses.description as name', 'subQuery.count', 'subQuery.total', \DB::raw('trafic_statuses.id as type'))
            ->leftJoinSub($subQuery, 'subQuery', function($join){
                $join->on('subQuery.trafic_status_id','=','trafic_statuses.id');
            });


        $result = $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name,
            'total' => $item->total ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 2) : 0,
            'type' => $item->type,
            'border_bottom' => $item->type == 4 ? 1 : 0,
        ]);

        $result->prepend($this->totalFasade($data));
        $result->push($this->planTarget($data));

        return $result;
    }

    private function totalFasade($data)
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->withTrashed(),
            ])
            ->withTrashed()
            ->where('trafics.client_type_id', '<>', 3)
            ->where('trafics.trafic_status_id', '<=', 4)
            ->filter($filter);

        $obj = $query->first();

        $result = [
            'count' => $obj->count ?? 0,
            'name' => 'Целевой трафик за период',
            'total' => $obj->count ?? 0,
            'percent' => $obj->count ? round((100 / $obj->count) * $obj->count, 2) : 0,
            'type' => 0,
            'border_top' => 1,
            'border_bottom' => 1,
        ];

        return $result;
    }

    private function planTarget($data)
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->withTrashed(),
            ])
            ->withTrashed()
            ->where('trafics.client_type_id', '<>', 3)
            ->where('trafics.trafic_status_id', '<=', 4)
            ->filter($filter);

        $obj = $query->first();

        $result = [];
        if(isset($data['show_month']))
        {
            $date = new Carbon($data['show_month']);
            if(now()->month = $date->month )
                $result = [
                    'count' => round($obj->count / now()->day * now()->endOfMonth()->day) ?? 0,
                    'name' => 'Целевой трафик (прогноз)',
                    'total' => $obj->count ?? 0,
                    'percent' => $obj->count ? round((100 / ($obj->count / now()->day * now()->endOfMonth()->day)) * $obj->count, 2) : 0,
                    'type' => 0,
                    'border_top' => 1,
                    'border_bottom' => 0,
                ];
            else
                $result = [
                    'count' => 100,
                    'name' => 'Целевой трафик (прогноз)',
                    'total' => 100,
                    'percent' => 100,
                    'type' => 0,
                    'border_top' => 1,
                    'border_bottom' => 0,
                ];
        }



        return $result;
    }
}
