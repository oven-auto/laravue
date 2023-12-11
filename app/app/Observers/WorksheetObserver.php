<?php

namespace App\Observers;

class WorksheetObserver
{
    public function created(\App\Models\Worksheet $worksheet)
    {
        $worksheet->trafic->process();
        $worksheet->executors()->attach($worksheet->author_id);
    }

    public function creating(\App\Models\Worksheet $worksheet)
    {

    }
}
