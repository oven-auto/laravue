<?php

namespace App\Services\TaskList;

use App\Http\Filters\ClientEventListFilter;
use App\Models\ClientEventStatus;
use App\Models\User;

Class EventTaskList
{
    public function getUserEventCount(User|int $user)
    {
        $events = ClientEventStatus::query()
            ->leftJoin('client_event_status_executors', 'client_event_status_executors.client_event_status_id', 'client_event_statuses.id')
            ->leftJoin('client_events', 'client_events.id', 'client_event_statuses.event_id')
            ->where(function($query) use ($user){
                $query->where(function($subQuery) use($user){
                    $subQuery->where('client_event_status_executors.user_id', $user);
                });
                $query->where('client_event_statuses.confirm', 'waiting');
                $query->whereDate('client_event_statuses.date_at', '<', now());
            })->count();

        return $events;
    }



    public function getEventsForTaskList(array $data)
    {
        $query = ClientEventStatus::query();

        $filter = app()->make(ClientEventListFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);
        
        $query->OnlyTableData()->WithEventAndTrafic()->ListOrder();

        $result = $query->get();

        return $result;
    }
}