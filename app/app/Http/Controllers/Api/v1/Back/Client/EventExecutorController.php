<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Event\Executor\ClientEventExecutorCollection;
use App\Models\ClientEventStatus;
use App\Models\User;
use App\Services\Comment\EventComment;
use Illuminate\Http\Request;

class EventExecutorController extends Controller
{
    public function index(ClientEventStatus $clientEventStatus, Request $request)
    {
        $executors = $clientEventStatus->event->executors;

        return new ClientEventExecutorCollection($executors->except(['id' => $clientEventStatus->event->author_id]));
    }

    public function store(ClientEventStatus $clientEventStatus, Request $request)
    {
        $neededUserId = [];

        if($request->has('executor_ids') && is_array($request->executor_ids))
        {
            foreach($request->executor_ids as $item)
                if(!$clientEventStatus->event->executors->contains('id', $item))
                    $neededUserId[] = $item;
            $clientEventStatus->event->executors()->attach($neededUserId);
        }

        $executors = User::whereIn('id', $neededUserId)->get();

        EventComment::addUsers($clientEventStatus, $executors);

        return (new ClientEventExecutorCollection($executors))
            ->additional(['message' => 'Данные добавлены',]);
    }

    public function delete(ClientEventStatus $clientEventStatus, Request $request)
    {
        $clientEventStatus->event->executors()->detach($request->executor_id);

        EventComment::delUser($clientEventStatus, User::findOrFail($request->executor_id));

        return response()->json([
            'message' => 'Исполнитель удален',
            'success' => 1,
            'executor' => $request->all()
        ]);
    }
}
