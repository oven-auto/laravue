<?php

namespace App\Services\Analytic;

use App\Http\Filters\WorksheetFilter;
use App\Models\Worksheet;

Class ClosedWorksheetAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        if(isset($data['register_begin']) || isset($data['register_end']))
        {
            $data['closed_begin'] = $data['register_begin'];
            $data['closed_end'] = $data['register_end'];
            unset($data['register_begin'], $data['register_end']);
        }

        $filter = app()->make(WorksheetFilter::class, ['queryParams' => array_filter($data)]);

        $query = Worksheet::select(
            \DB::raw('"Закрытые в периоде" as name'),
            \DB::raw('count(worksheets.id) as count'),
            \DB::raw('1 as type')
        )->leftJoin('worksheet_actions', function($join) {
            $join->on('worksheet_actions.worksheet_id','worksheets.id');
        })->where(
            'worksheet_actions.id',
            \DB::raw('(SELECT max(SWA.id) FROM worksheet_actions as SWA WHERE SWA.worksheet_id = worksheets.id)')
        )->filter($filter);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name,
            'total' => $item->count ?? 0,
            'percent' => $item->count ? round((100 / $item->count) * $item->count, 2) : 0,
            'type' => $item->type
        ]);
    }
}
