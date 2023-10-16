<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Event\ReportResource;
use App\Models\ClientEventStatus;
use Illuminate\Http\Request;

class EventReportController extends Controller
{
    public function report(Request $request)
    {
        $eventStatus = ClientEventStatus::find($request->get('id'));

        if($eventStatus->reporters->contains('id', auth()->user()->id))
            throw new \Exception('Вы уже отчитались за данное действие');

        $eventStatus->reporters()->attach(
            auth()->user()->id, ['created_at' => now()]
        );

        $eventStatus->load('reporters');

        return (new ReportResource($eventStatus))
            ->additional(['message' => 'Вы успешно отчитались']);
    }

    public function deport(Request $request)
    {
        $eventStatus = ClientEventStatus::find($request->get('id'));

        if(!$eventStatus->reporters->contains('id', $request->get('reporter_id')))
            throw new \Exception('Пользователя нет в списке отчитавшихся');

        if($eventStatus->reporters->contains('id', $request->get('reporter_id')))
            $eventStatus->reporters()->detach($request->get('reporter_id'));

        $eventStatus->load('reporters');

        return (new ReportResource($eventStatus))
            ->additional(['message' => 'Отчет отменен']);
    }
}
