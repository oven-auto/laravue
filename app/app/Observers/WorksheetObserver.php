<?php

namespace App\Observers;

use App\Events\WorksheetCreateEvent;

class WorksheetObserver
{
    public function created(\App\Models\Worksheet $worksheet)
    {
        $worksheet->trafic->process();

        $worksheet->executors()->attach($worksheet->author_id);

        //WorksheetCreateEvent::dispatch($worksheet);
    }

    

    public function creating(\App\Models\Worksheet $worksheet)
    {

    }
}
