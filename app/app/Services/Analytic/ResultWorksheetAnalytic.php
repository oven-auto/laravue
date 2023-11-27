<?php

namespace App\Services\Analytic;

use App\Http\Filters\WorksheetAnalyticFilter;
use App\Models\Task;
use App\Models\Worksheet;
use Illuminate\Support\Arr;

Class ResultWorksheetAnalytic implements TraficAnalyticInterface
{
    public function getArrayAnalytic(array $data)
    {
        $arr = Arr::except($data, ['interval_begin','interval_end']);
        $arr['closed_begin'] = $data['interval_begin'];
        $arr['closed_end'] = $data['interval_end'];

        $filter = app()->make(WorksheetAnalyticFilter::class, ['queryParams' => array_filter($arr)]);

        $subQuery = Worksheet::select([
                \DB::raw('count(worksheets.id) as count'),
                'worksheet_actions.task_id as task_id',
            ])
            ->whereIn('worksheet_actions.task_id', [6,7])
            ->filter($filter)
            ->groupBy('worksheet_actions.task_id');

        if(collect($subQuery->getQuery()->joins)->pluck('table')->contains('worksheet_executors'))
            $subQuery->whereRaw(\DB::raw('worksheets.author_id = worksheet_executors.user_id'));

        $query = Task::select()
            ->leftJoinSub($subQuery, 'subQuery', function($join){
                $join->on('subQuery.task_id','=','tasks.id');
            })->whereIn('tasks.id', [6,7]);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name,
            'total' => $item->count ?? 0,
            'percent' => $item->count ? round((100 / $item->count) * $item->count, 2) : 0,
            'type' => $item->task_id,
            'inversion' => $item->task_id == 7 ? 1 : 0,
        ]);
    }
}











// select * from `tasks`

// left join (
//     select
//         `worksheet_actions`.`task_id` as `task_id`,
//         COUNT(worksheet_actions.task_id) as count,
//         (
//             select count(*) from `worksheets`
//             where `worksheets`.`appeal_id` in (2, 3)
//             and date(`worksheets`.`created_at`) >= 2023-10-01
//             and date(`worksheets`.`created_at`) <= 2023-10-31
//         ) as `total`
//     from `worksheets`
//     left join `worksheet_actions` on `worksheet_actions`.`task_id` = `tasks`.`id`
//     where `worksheet_actions`.`id` = (
//         SELECT max(SWA.id) FROM worksheet_actions as SWA
//         WHERE SWA.worksheet_id = worksheets.id
//     ) and `worksheet_actions`.`task_id` in (6, 7) and `worksheets`.`appeal_id` in (2, 3)
//     and date(`worksheets`.`created_at`) >= 2023-10-01 and date(`worksheets`.`created_at`) <= 2023-10-31) as `subQuery`
// on `subQuery`.`task_id` = `tasks`.`id`
