<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Event\Reporter\ClientEventReporterCollection;
use App\Http\Resources\Client\Event\Reporter\ClientEventReporterResource;
use App\Models\ClientEventStatus;
use App\Models\User;
use App\Services\Comment\EventComment;
use Illuminate\Http\Request;

class EventReportController extends Controller
{
    public function report(Request $request)
    {
        $eventStatus = ClientEventStatus::find($request->get('id'));

        if($eventStatus->reporters->contains('id', auth()->user()->id))
            throw new \Exception('Вы уже отчитались за данное действие');

        $eventStatus->event->executors()->detach(auth()->user()->id);

        $eventStatus->reporters()->attach(
            auth()->user()->id, ['created_at' => now()]
        );

        EventComment::reportlUser($eventStatus, auth()->user());

        \App\Classes\Telegram\Notice\TelegramNotice::run($eventStatus)->report()->send([$eventStatus->event->author_id]);

        return (new ClientEventReporterResource(auth()->user()))
            ->additional(['message' => 'Вы успешно отчитались', 'success' => 1]);
    }

    public function deport(Request $request)
    {
        $eventStatus = ClientEventStatus::find($request->get('id'));

        if(!$eventStatus->reporters->contains('id', $request->get('reporter_id')))
            throw new \Exception('Пользователя нет в списке отчитавшихся');

        if($eventStatus->reporters->contains('id', $request->get('reporter_id')))
        {
            $eventStatus->reporters()->detach($request->get('reporter_id'));

            if(!$eventStatus->event->executors->contains('id', $request->get('reporter_id')))
                $eventStatus->event->executors()->attach($request->get('reporter_id'));
        }

        $user = User::findOrFail($request->get('reporter_id'));

        EventComment::deportUser($eventStatus, $user);

        return (new ClientEventReporterResource($user))
            ->additional(['message' => 'Отчет отменен', 'success' => 1]);
    }
}
