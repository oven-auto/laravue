<?php

namespace App\Services\TaskList;

use App\Models\Trafic;
use App\Models\User;

Class TraficTaskList 
{
    /**
     * Кол-во трафиков ожидающих пользователя
     */
    public function getUserTraficCount(User|int $user)
    {
        $traficWaitingStatuses = [1,2,6];
        
        $traficCount = Trafic::query()
            ->leftJoin('trafic_controls', 'trafic_controls.trafic_id', 'trafics.id')
            ->where(function($query) use ($user, $traficWaitingStatuses){
                $query->where('trafics.manager_id', $user);
                $query->whereIn('trafics.trafic_status_id', $traficWaitingStatuses);
                $query->where('trafic_controls.end_at', '<', now());
            })->count();

        return $traficCount;
    }



    /**
     * ПОЛУЧИТЬ СПИСОК ТРАФИКОВ ДЛЯ ЖУРНАЛА ЗАДАЧ
     * @param $data данные для фильтрации
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTraficsForTaskList(array $data): \Illuminate\Database\Eloquent\Collection
    {
        $query = Trafic::select('trafics.*')->withTrashed();

        $filter = app()->make(\App\Http\Filters\TraficListFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $query->with([
            'needs','zone', 'chanel.myparent',
            'salon', 'structure', 'appeal', 'manager',
            'author', 'client', 'control', 'message'
        ]);

        $query->orderBy('trafic_controls.begin_at')->groupBy('trafics.id');

        $trafics = $query->get();

        return $trafics;
    }
}