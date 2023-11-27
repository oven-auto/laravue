<?php

namespace App\Services\Analytic;

use App\Models\Worksheet;

Class WorksheetAuthor
{
    public static function getCountAnalyticByAuthor($dataFilter)
    {
        $filter = app()->make(\App\Http\Filters\WorksheetAnalyticFilter::class, ['queryParams' => array_filter($dataFilter)]);

        $count = Worksheet::select([\DB::raw('COUNT(worksheets.id) as count'),])
            ->where('worksheets.status_id','work')
            ->filter($filter)
            ->groupBy('worksheets.id')
            ->get()
            ->count();

        $query = Worksheet::query()
            ->select([
                \DB::raw("CONCAT(users.lastname, ' ', users.name) as name"),
                \DB::raw("COUNT(worksheets.id) as count")
            ])
            ->leftJoin('users', 'users.id', 'worksheets.author_id')
            ->filter($filter);

        if(collect($query->getQuery()->joins)->pluck('table')->contains('worksheet_executors'))
            $query->whereRaw(\DB::raw('users.id = worksheet_executors.user_id'));

        $query->where('worksheets.status_id', 'work');

        $query->groupBy('worksheets.author_id');

        $result = $query->get()->map(function($item) use ($count){
            return [
                'name' => $item['name'],
                'count' => $item['count'],
                'percent' => number_format( (100 / $count * $item['count']), 1, '.', ''),
            ];
        });

        return $result;
    }
}
