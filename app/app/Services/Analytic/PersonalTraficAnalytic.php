<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;

Class PersonalTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
                \DB::raw('concat(users.name," ", users.lastname) as name'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->withTrashed()->onlyTarget(),
                \DB::raw('users.id as type')
            ])
            ->withTrashed()
            ->onlyTarget()
            ->leftJoin('users', 'users.id', 'trafics.manager_id')
            ->groupBy('trafics.manager_id')
            ->filter($filter);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name ?? '[ Ожидающий ]',
            'total' => $item->total ?? 0,
            'percent' => $item->total ? round((100 / $item->total) * $item->count, 2) : 0,
            'type' => $item->type
        ]);
    }
}
