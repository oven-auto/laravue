<?php

namespace App\Services\Client;
use App\Models\ClientEventStatus;
use App\Models\Trafic;
use App\Repositories\Trafic\TraficRepository;

Class EventTrafic
{
    private $trafic;
    private $traficRepo;

    public function __construct(Trafic $trafic, TraficRepository $repo)
    {
        $this->trafic = $trafic;
        $this->traficRepo = $repo;
    }
    public function createTrafic(ClientEventStatus $eventStatus, bool $status)
    {
        if(!$status)
            return false;

        $client = $eventStatus->event->client;
        $clientArr = $client->toArray();
        $clientArr['author_id'] = auth()->user()->id;
        $clientArr['phone'] = $client->phones->count() ? $client->phones->first()->phone : '';
        $clientArr['email'] = $client->emails->count() ? $client->emails->first()->email : '';
        $clientArr['inn'] = $client->inn->first()->number;
        $clientArr['trafic_chanel_id'] = 37;

        $this->traficRepo->save($this->trafic, $clientArr);
        \App\Models\ClientEventTrafic::create([
            'trafic_id' => $this->trafic->id,
            'event_id' => $eventStatus->event_id,
            'event_status_id' => $eventStatus->id
        ]);
        return $this->trafic->id;
    }

    public static function addTrafic(ClientEventStatus $eventStatus, bool $status)
    {
        $me = new EventTrafic(new Trafic, new TraficRepository);
        return $me->createTrafic($eventStatus, $status);

    }
}
