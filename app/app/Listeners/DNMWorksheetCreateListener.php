<?php

namespace App\Listeners;

use App\Classes\LadaDNM\DNMWorksheetService;
use App\Events\WorksheetCreateEvent;
use App\Jobs\CreateDNMWorksheetJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DNMWorksheetCreateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\WorksheetCreateEvent  $event
     * @return void
     */
    public function handle(WorksheetCreateEvent $event)
    {
        CreateDNMWorksheetJob::dispatch($event->worksheet);
    }
}
