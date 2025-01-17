<?php

namespace App\Observers\Worksheet\Modules;

use App\Events\ClientCreateOrUpdateEvent;
use App\Events\DNMVisitEvent;
use App\Events\ReserveCreateEvent;
use App\Events\WorksheetCreateEvent;
use App\Models\WsmReserveNewCar;

class ReserveNewCarObserver
{
    public function creating(WsmReserveNewCar $reserve)
    {
        if($reserve->worksheet->client->isCompany() && $reserve->worksheet->subclients->count() == 0)
            throw new \Exception('У юр.лица отсутствует контактное лицо.');

        if($reserve->worksheet->client->isPerson() && !$reserve->worksheet->client->firstname)
            throw new \Exception('У физ.лица отсутствует имя.');
    }



    public function created(WsmReserveNewCar $reserve)
    {
        if($reserve->worksheet->isLada() && $reserve->worksheet->isSaleDepartment() && $reserve->worksheet->isSaleNewCar())
            ReserveCreateEvent::dispatch($reserve);
    }



    public function deleted(WsmReserveNewCar $reserve)
    {
        if($reserve->worksheet->isLada() && $reserve->worksheet->isSaleDepartment() && $reserve->worksheet->isSaleNewCar())
            DNMVisitEvent::dispatch($reserve, 'reject');
    }
}
