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
        $txt =  $event->message.PHP_EOL;
        $txt .= "Номер трафика: {$event->data->id} ".PHP_EOL;
        $txt .= "Автор трафика: {$event->data->author->cut_name} ".PHP_EOL;
        $txt .= "Менеджер трафика: {$event->data->manager->cut_name} ".PHP_EOL;
        $txt .= PHP_EOL;
        $txt .= "Автор изменения: ".auth()->user()->cut_name.PHP_EOL;

        try{
            \App\Classes\Socket\SocketClient::socket(
                json_encode(
                    ['auth' => 1, 'message' => $txt]
                )
            )->send();
        } catch(\Exception $e){

        }
    }
}
