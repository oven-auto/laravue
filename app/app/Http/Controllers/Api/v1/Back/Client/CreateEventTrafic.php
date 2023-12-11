<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientEventStatus;
use App\Services\Client\EventTrafic;
use Illuminate\Http\Request;

class CreateEventTrafic extends Controller
{
    /**
     * Создать трафик из коммуникации
     * @param Request $request [event_status_id => id]
     */
    public function __invoke(Request $request)
    {
        $eventStatus = ClientEventStatus::findOrFail($request->get('event_status_id'));

        $traficId = EventTrafic::addTrafic($eventStatus);

        $eventStatus->lastComment()->create([
            'author_id' => auth()->user()->id,
            'text' => 'Создано обращение №'.$traficId,
            'event_id' => $eventStatus->event_id,
            'client_event_status_id' => $eventStatus->id
        ]);

        return (new \App\Http\Resources\Client\EventSaveResource($eventStatus))
            ->additional([
                'data' => ['trafic_id' => $traficId],
                'message' => $eventStatus->lastComment->text,
                'request' => $request->all(),
            ]);
    }
}
