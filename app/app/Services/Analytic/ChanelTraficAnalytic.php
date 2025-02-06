<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;
use App\Models\TraficChanel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;

Class ChanelTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                    DB::raw('COUNT(trafics.id) as count'),
                    DB::raw('trafic_chanels.name as name'),
                    'total' => Trafic::select(DB::raw('count(*)'))->filter($filter)->withTrashed()->onlyTarget(),
                    DB::raw('trafic_chanels.id as type')
            ])
            ->withTrashed()
            ->onlyTarget()
            ->rightJoin('trafic_chanels', 'trafic_chanels.id', 'trafics.trafic_chanel_id')
            ->groupBy('trafic_chanels.id')
            ->filter($filter);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name ?? 'Не назначено',
            'total' => $item->total ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 2) : 0,
            'type' => $item->type
        ]);
    }
}
