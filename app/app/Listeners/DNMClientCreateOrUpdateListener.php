<?php

namespace App\Listeners;

use App\Classes\LadaDNM\DNMClientService;
use App\Events\ClientCreateOrUpdateEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DNMClientCreateOrUpdateListener
{
    public $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DNMClientService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ClientCreateOrUpdateEvent  $event
     * @return void
     */
    public function handle(ClientCreateOrUpdateEvent $event)
    {
        Log::alert('Пробую отправить данные о клиенте на ДНМ');
        
        $this->service->save($event->client);
    }
}
