<?php

namespace App\Listeners;

use App\Classes\LadaDNM\DNMWorksheetService;
use App\Events\WorksheetCreateEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DNMWorksheetCreateListener
{
    public $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DNMWorksheetService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\WorksheetCreateEvent  $event
     * @return void
     */
    public function handle(WorksheetCreateEvent $event)
    {
        if($event->worksheet->isLada() && $event->worksheet->isSaleDepartment() && $event->worksheet->isSaleNewCar())
            $this->service->save($event->worksheet);
    }
}
