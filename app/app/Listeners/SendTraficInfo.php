<?php

namespace App\Listeners;

use App\Events\TraficEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTraficInfo
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TraficEvent  $event
     * @return void
     */
    public function handle(TraficEvent $event)
    {
        $txt = "Изменен трафик ".PHP_EOL;
        $txt .= "Номер трафика: {$event->data->id} ".PHP_EOL;
        $txt .= "Автор трафика: {$event->data->author->cut_name} ".PHP_EOL;
        $txt .= "Менеджер трафика: {$event->data->manager->cut_name} ".PHP_EOL;

        \App\Classes\Socket\SocketClient::socket($txt)->send();
    }
}
