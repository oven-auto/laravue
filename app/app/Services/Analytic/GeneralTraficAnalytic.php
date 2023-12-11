<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\ClientType;
use App\Models\Trafic;

Class GeneralTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic($data = [])
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $subQuery = Trafic::select([
                'trafics.client_type_id',
                \DB::raw('COUNT(trafics.client_type_id) as count'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->withTrashed(),
            ])
            ->where('trafics.trafic_status_id', '<>', 6)
            ->filter($filter)
            ->groupBy('trafics.client_type_id');

        $query = ClientType::select('client_types.name', 'subQuery.count', 'subQuery.total', \DB::raw('client_types.id as type'))
            ->leftJoinSub($subQuery, 'subQuery', function($join){
                $join->on('subQuery.client_type_id','=','client_types.id');
            });

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name.'',
            'total' => $item->total ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 2) : 0,
            'type' => $item->type
        ]);
    }
}
