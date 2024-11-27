<?php

namespace App\Observers\Worksheet\Modules;

use App\Events\ClientCreateOrUpdateEvent;
use App\Events\DNMVisitEvent;
use App\Events\ReserveCreateEvent;
use App\Events\WorksheetCreateEvent;
use App\Models\WsmReserveNewCar;

class ReserveNewCarObserver
{
    public function created(WsmReserveNewCar $reserve)
    {
        if($reserve->worksheet->isLada() && $reserve->worksheet->isSaleDepartment() && $reserve->worksheet->isSaleNewCar())
        {
            ClientCreateOrUpdateEvent::dispatch($reserve->worksheet->client);

            WorksheetCreateEvent::dispatch($reserve->worksheet);

            ReserveCreateEvent::dispatch($reserve);
        }
    }



    public function deleted(WsmReserveNewCar $reserve)
    {
        DNMVisitEvent::dispatch($reserve, 'reject');
    }
}
