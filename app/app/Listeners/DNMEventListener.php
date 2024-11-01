<?php

namespace App\Listeners;

use App\Classes\LadaDNM\DNMEvent;
use App\Events\DNMVisitEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DNMEventListener
{
    public $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DNMEvent $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\DNMVisitEvent  $event
     * @return void
     */
    public function handle(DNMVisitEvent $event)
    {
        $this->service->handler($event->reserve, $event->eventType);
    }
}
