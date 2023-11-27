<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;

Class TotalTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
            ])
            ->withTrashed()
            ->filter($filter);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => 'Все обращения за период',
            'total' => $item->count ?? 0,
            'percent' => $item->count ? round((100 / $item->count) * $item->count, 1) : 0,
            'type' => $item->type ?? 1
        ]);
    }
}
