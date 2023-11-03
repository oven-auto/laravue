<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficFilter;
use App\Models\Trafic;

Class AuthorTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
                \DB::raw('concat(users.name," ", users.lastname) as name'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->withTrashed(),
                \DB::raw('trafics.author_id as type')
            ])
            ->withTrashed()
            ->leftJoin('users', 'users.id', 'trafics.author_id')
            ->groupBy('trafics.author_id')
            ->filter($filter);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name,
            'total' => $item->count ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 2) : 0,
            'type' => $item->type
        ]);
    }
}
