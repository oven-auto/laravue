<?php

namespace App\Listeners;

use App\Classes\LadaDNM\DNMAppealService;
use App\Events\ReserveCreateEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DNMReserveCreateListener
{
    public $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DNMAppealService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ReserveCreateEvent  $event
     * @return void
     */
    public function handle(ReserveCreateEvent $event)
    {
        $this->service->save($event->reserve);
    }
}
