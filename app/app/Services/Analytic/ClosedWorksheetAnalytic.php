<?php

namespace App\Services\Analytic;

use App\Http\Filters\WorksheetAnalyticFilter;
use App\Models\Worksheet;
use Illuminate\Support\Arr;

Class ClosedWorksheetAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $arr = Arr::except($data, ['interval_begin','interval_end']);
        $arr['closed_begin'] = $data['interval_begin'];
        $arr['closed_end'] = $data['interval_end'];

        $filter = app()->make(WorksheetAnalyticFilter::class, ['queryParams' => array_filter($arr)]);

        $query = Worksheet::select(
            \DB::raw('"Завершенные в периоде" as name'),
            \DB::raw('count(worksheets.id) as count'),
            \DB::raw('1 as type')
        )
        ->filter($filter);

        if(collect($query->getQuery()->joins)->pluck('table')->contains('worksheet_executors'))
            $query->whereRaw(\DB::raw('worksheets.author_id = worksheet_executors.user_id'));

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name,
            'total' => $item->count ?? 0,
            'percent' => $item->count ? round((100 / $item->count) * $item->count, 2) : 0,
            'type' => $item->type
        ]);
    }
}
