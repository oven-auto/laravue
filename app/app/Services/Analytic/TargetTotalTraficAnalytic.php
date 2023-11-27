<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;

Class TargetTotalTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter),
            ])
            ->onlyTarget()
            ->filter($filter);

        return $query->get()->map(fn($obj) => [
                'count' => $obj->count ?? 0,
                'name' => 'Целевой трафик за период',
                'total' => $obj->total ?? 0,
                'percent' => $obj->count ? round((100 / $obj->count) * $obj->count, 2) : 0,
                'type' => 0,
                'border_top' => 1,
                'border_bottom' => 1,
            ]
        );
    }
}
