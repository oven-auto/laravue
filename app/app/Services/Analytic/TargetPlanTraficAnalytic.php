<?php

namespace App\Services\Analytic;

use App\Http\Filters\TraficFilter;
use App\Models\Trafic;
use Carbon\Carbon;

Class TargetPlanTraficAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select([
                \DB::raw('COUNT(trafics.id) as count'),
                'total' => Trafic::select(\DB::raw('count(*)'))->filter($filter)->onlyTarget(),
            ])
            ->onlyTarget()
            ->filter($filter);

        $result = $query->get()->map(fn($item) => [
            'count' => round($item->count / now()->day * now()->endOfMonth()->day) ?? 0,
            'name' => 'Целевой трафик (прогноз)',
            'total' => $item->total ?? 0,
            'percent' => 100,//$item->total ? round((100 / ($item->total / now()->day * now()->endOfMonth()->day)) * $item->count, 2) : 0,
            'type' => 0,
            'border_top' => 0,
            'border_bottom' => 0,
        ]);

        // $result = [];
        // if(isset($data['show_month']))
        // {
        //     $date = new Carbon($data['show_month']);
        //     if(now()->month = $date->month )
        //         $result = [
        //             'count' => round($obj->count / now()->day * now()->endOfMonth()->day) ?? 0,
        //             'name' => 'Целевой трафик (прогноз)',
        //             'total' => $obj->count ?? 0,
        //             'percent' => $obj->count ? round((100 / ($obj->count / now()->day * now()->endOfMonth()->day)) * $obj->count, 2) : 0,
        //             'type' => 0,
        //             'border_top' => 1,
        //             'border_bottom' => 0,
        //         ];
        //     else
        //         $result = [
        //             'count' => 100,
        //             'name' => 'Целевой трафик (прогноз)',
        //             'total' => 100,
        //             'percent' => 100,
        //             'type' => 0,
        //             'border_top' => 1,
        //             'border_bottom' => 0,
        //         ];
        // }
    }
}
