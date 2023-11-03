<?php

namespace App\Services\Analytic;

use App\Http\Filters\WorksheetFilter;
use App\Models\Task;
use App\Models\Worksheet;

Class ResultWorksheetAnalytic implements TraficAnalyticInterface
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

        $subQuery = Worksheet::select([
                'worksheet_actions.task_id as task_id',
                \DB::raw('COUNT(worksheet_actions.task_id) as count'),
                'total' => Worksheet::select(\DB::raw('count(*)'))->filter($filter),
            ])->leftJoin('worksheet_actions', function($join) {
                $join->on('worksheet_actions.worksheet_id','worksheets.id');
            })->where(
                'worksheet_actions.id',
                \DB::raw('(SELECT max(SWA.id) FROM worksheet_actions as SWA WHERE SWA.worksheet_id = worksheets.id)')
            )->whereIn('worksheet_actions.task_id', [6,7])
            ->filter($filter)
            ->groupBy('worksheet_actions.task_id')->groupBy('worksheet_actions.status')->groupBy('worksheet_actions.created_at');

        $query = Task::select()
            ->leftJoinSub($subQuery, 'subQuery', function($join){
                $join->on('subQuery.task_id','=','tasks.id');
            })->whereIn('tasks.id', [6,7]);

        return $query->get()->map(fn($item) => [
            'count' => $item->count ?? 0,
            'name' => $item->name,
            'total' => $item->count ?? 0,
            'percent' => $item->count ? round((100 / $item->count) * $item->count, 2) : 0,
            'type' => $item->type
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
