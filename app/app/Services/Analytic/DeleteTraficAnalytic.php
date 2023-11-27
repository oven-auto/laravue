<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;

Class DeleteTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->withTrashed(),
            ])
            ->onlyTrashed()
            ->filter($filter);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => 'Удаленные за период',
            'total' => $item->total ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 1) : 0,
            'type' => $item->type ?? 1,
            'border_top' => 0,
            'border_bottom' => 0,
            'inversion' => 1,
        ]);
    }
}
