<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
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

        $traficId = EventTrafic::addTrafic($eventStatus, $request->has('with_trafic'));

        return (new \App\Http\Resources\Client\EventSaveResource($eventStatus))
            ->additional([
                'data' => ['trafic_id' => $traficId],
                'message' => $eventStatus->lastComment->text
            ]);
    }
}
