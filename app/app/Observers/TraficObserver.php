<?php

namespace App\Observers;

class TraficObserver
{
    public function updating(\App\Models\Trafic $trafic)
    {
        $current = $trafic->trafic_status_id;
        $old = $trafic->getOriginal('trafic_status_id');

        if($current != $old && ($current == 4 || $current == 3))
            $trafic->processing_at = now();
    }

    public function saving(\App\Models\Trafic $trafic)
    {
        $currentMn = $trafic->manager_id;
        $oldMn = $trafic->getOriginal('manager_id');

        if($currentMn != $oldMn && $trafic->trafic_status_id != 4)
            $trafic->trafic_status_id = 2;
    }

    public function deleted(\App\Models\Trafic $trafic)
    {
        $trafic->trafic_status_id = 5;
        $trafic->save();
    }
}
