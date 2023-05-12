<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\Trafic;
use Illuminate\Http\Request;
use App\Models\ClientEvent;

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

        if($eventStatus == false)
            throw new \Exception('Это событие уже было выполнено');

        $client = $eventStatus->event->client;

        if($request->has('with_trafic')) {
            $clientArr = $client->toArray();
            $clientArr['author_id'] = auth()->user()->id;
            $clientArr['phone'] = $client->phones->first()->phone;
            $clientArr['email'] = $client->emails->count() ? $client->emails->first()->email : '';
            $clientArr['trafic_chanel_id'] = 37;

            $trafic = new Trafic();
            $this->traficRepo->save($trafic, $clientArr);
            \App\Models\ClientEventTrafic::create([
                'trafic_id' => $trafic->id,
                'event_id' => $eventStatus->event_id,
                'event_status_id' => $eventStatus->id
            ]);
        }
        return (new \App\Http\Resources\Client\EventSaveResource($eventStatus))
            ->additional([
                'data' => ['trafic_id' => isset($trafic) ? $trafic->id : ''],
                'message' => $eventStatus->lastComment->text
            ]);
    }
}
