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

    private function filter($data = []) :  \Illuminate\Database\Eloquent\Builder
    {
        $query = Worksheet::select('worksheets.*');
        $filter = app()->make(\App\Http\Filters\WorksheetFilter::class, ['queryParams' => array_filter($data)]);
        return $query
            ->filter($filter);
    }

    public function paginate(array $data, $paginate = 20)
    {
        $query = $this->filter($data)
            ->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type'])
            ->leftJoin('worksheet_actions', function($join) {
                $join->on('worksheet_actions.worksheet_id','worksheets.id');
                $join->on('worksheet_actions.end_at', \DB::raw(
                    '(SELECT wa.end_at FROM worksheet_actions as wa
                    where wa.worksheet_id = worksheets.id
                    order by wa.end_at DESC LIMIT 1)')
                );
            })
            //->where('worksheet_actions.task_id',1)
            ->groupBy('worksheets.id')
            ->groupBy('worksheet_actions.end_at')
            ->orderBy('worksheet_actions.end_at', 'DESC');
            //->toSql();
       // dd($query);
        $result = $query->simplePaginate($paginate);

        return $result;
    }
}
