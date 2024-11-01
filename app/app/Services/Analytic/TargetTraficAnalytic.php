<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;
use App\Models\TraficStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

Class TargetTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic($data = [])
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $subQuery = Trafic::select([
                'trafics.trafic_status_id',
                DB::raw('COUNT(trafics.trafic_status_id) as count'),
                'total' => Trafic::select(DB::raw('count(*)'))
                    ->filter($filter)
                    ->withTrashed()
                    ->onlyTarget()
            ])
            ->withTrashed()
            ->onlyTarget()
            ->filter($filter)
            ->groupBy('trafics.trafic_status_id');

        $query = TraficStatus::select('trafic_statuses.description as name', 'subQuery.count', 'subQuery.total', DB::raw('trafic_statuses.id as type'))
            ->leftJoinSub($subQuery, 'subQuery', function($join){
                $join->on('subQuery.trafic_status_id','=','trafic_statuses.id');
            })->whereIn('trafic_statuses.id', [1,2,3,4]);

        $result = $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name,
            'total' => $item->total ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 2) : 0,
            'type' => $item->type,
            'border_bottom' => 0,
            'inversion' => $item->type == 4 ? 1 : 0
        ]);

        return $result;
    }
}
