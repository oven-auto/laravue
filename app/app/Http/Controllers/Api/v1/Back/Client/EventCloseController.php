<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientEventStatus;
use App\Services\Client\EventTrafic;
use Illuminate\Http\Request;

class EventCloseController extends Controller
{
    private $eventClose;
    private $traficRepo;

    public function __construct(\App\Services\Client\EventClose $eventClose, \App\Repositories\Trafic\TraficRepository $traficRepository)
    {
        $this->eventClose = $eventClose;
        $this->traficRepo = $traficRepository;
    }

    public function close(Request $request)
    {

        $eventStatus = $this->eventClose->close($request->get('event_status_id'));

        $arrUser = [];
        $users = $eventStatus->event->executors;
        foreach($users as $item)
            $arrUser[] = $item->id;

        \App\Classes\Telegram\Notice\TelegramNotice::run($eventStatus)->close()->send($arrUser);

        return (new \App\Http\Resources\Client\EventSaveResource($eventStatus))
            ->additional([
                'message' => $eventStatus->lastComment->text
            ]);
    }

    public function resume(Request $request)
    {
        $eventStatus = $this->eventClose->resume($request->get('event_status_id'));

        return (new \App\Http\Resources\Client\EventSaveResource($eventStatus))
            ->additional([
                'message' => $eventStatus->lastComment->text
            ]);
    }
}
