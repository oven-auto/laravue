<?php

namespace App\Repositories\Worksheet;

use App\Models\Task;
use App\Models\Worksheet;
use App\Models\Trafic;
use App\Repositories\Client\ClientRepository;
use App\Services\Worksheet\ActionSave;

class WorksheetRepository
{
    public function createFromTrafic($trafic_id)
    {
        $trafic = Trafic::with('status')->find($trafic_id);



        $client = ClientRepository::getClientFromTrafic($trafic);

        $worksheet = Worksheet::create([
            'client_id'         => $client->id,
            'trafic_id'         => $trafic->id,
            'company_id'        => $trafic->salon->id,
            'structure_id'      => $trafic->structure->id,
            'appeal_id'         => $trafic->appeal->id,
            'author_id'         => $trafic->manager_id,
            'status_id'         => \App\Models\WorksheetStatus::where('slug','work')->first()->id,
        ]);

        $dataTraficAction = [
            'task_id' => Task::where('slug','control')->first()->id,
            'worksheet_id' => $worksheet->id,
            'begin_at' => $trafic->begin_at,
            'end_at' => $trafic->end_at,
            'author_id' => auth()->user()->id,
            'text' => $trafic->comment ? $trafic->comment : 'Комментарий трафика отсутствует',
        ];
        $actionService = new ActionSave();
        $actionService->saveActionFasade($dataTraficAction);

        $worksheet->trafic->status;

        return $worksheet;
    }
}
