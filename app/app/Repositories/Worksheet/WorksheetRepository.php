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
            'status_id'         => 'work',
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

    private function filter($query, $data = []) :  \Illuminate\Database\Eloquent\Builder
    {
        $query->leftJoin('worksheet_actions', function($join) {
                $join->on('worksheet_actions.worksheet_id','worksheets.id');
            })
            ->where(
                'worksheet_actions.id',
                \DB::raw('(SELECT max(SWA.id) FROM worksheet_actions as SWA WHERE SWA.worksheet_id = worksheets.id)')
            );

        $filter = app()->make(\App\Http\Filters\WorksheetFilter::class, ['queryParams' => array_filter($data)]);

        return $query->filter($filter);
    }

    public function paginate(array $data, $paginate = 20)
    {
        $this->setDefaultStatus($data);

        $query = Worksheet::query()->select('worksheets.*');

        $query->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type']);

        $this->filter($query,$data);

        $query->orderBy('worksheet_actions.end_at', 'ASC');

        $query->groupBy('worksheet_actions.end_at')->groupBy('worksheets.id');

        $result = $query->simplePaginate($paginate);

        return $result;
    }

    public function get(array $data)
    {
        $query = Worksheet::query()->select('worksheets.*');

        $query->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type']);

        $this->filter($query,$data);

        $query->groupBy('worksheets.id')->groupBy('worksheet_actions.begin_at');

        $result = $query->orderBy('worksheet_actions.begin_at')->get();

        return $result;
    }

    public function counter(array $data)
    {
        $this->setDefaultStatus($data);

        $query = Worksheet::query();

        $subQuery = Worksheet::query()->select('worksheets.id');

        $this->filter($subQuery,$data);

        $query->rightJoinSub($subQuery, 'subQuery', function($join){

            $join->on('subQuery.id','=','worksheets.id');

        });

        $result = $query->count();

        return $result;
    }

    public function setDefaultStatus(&$data)
    {
        // if(!isset($data['status_ids']))
        //     $data['status_ids'] = ['work', 'check'];
    }
}
