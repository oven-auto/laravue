<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;
use Illuminate\Support\Facades\DB;

Class ChanelTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                DB::raw('COUNT(trafics.id) as count'),
                DB::raw('trafic_chanels.name as name'),
                'total' => Trafic::select(DB::raw('count(*)'))->filter($filter)->withTrashed()->onlyTarget(),
            ])
            ->withTrashed()
            ->onlyTarget()
            ->leftJoin('trafic_chanels', 'trafic_chanels.id', 'trafics.trafic_chanel_id')
            ->groupBy('trafics.trafic_chanel_id')
            ->filter($filter);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name ?? 'Не назначено',
            'total' => $item->total ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 2) : 0,
            'type' => ''
        ]);
    }
}
