<?php

namespace App\Observers;

class WorksheetObserver
{
    public function created(\App\Models\Worksheet $worksheet)
    {
        $worksheet->trafic->process();
    }
}
