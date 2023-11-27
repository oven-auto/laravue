<?php

namespace App\Repositories\Worksheet;

use App\Models\Task;
use App\Models\Worksheet;
use App\Models\Trafic;
use App\Repositories\Client\ClientRepository;
use App\Services\Comment\Comment;
use App\Services\Worksheet\ActionSave;

class WorksheetRepository
{
    /**
     * Создать РЛ из трафика
     * @param int $trafic_id
     * @return Worksheet
     */
    public function createFromTrafic(int $trafic_id) : Worksheet
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

        $worksheet->last_action()->create([
            'task_id' => Task::where('slug','control')->first()->id,
            'worksheet_id' => $worksheet->id,
            'begin_at' => $trafic->begin_at,
            'end_at' => $trafic->end_at,
            'author_id' => auth()->user()->id,
        ]);

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

    public function workingCount($data)
    {
        $data['status_ids'] = ['work'];
        $count = $this->counter($data);
        return $count;
    }

    public function setDefaultStatus(&$data)
    {

    }

    /**
     * Получить список РЛ для журнала задач
     * @param array $data
     */
    public function getWorksheetsForTaskList(array $data)
    {
        $filter = app()->make(\App\Http\Filters\WorksheetListFilter::class, ['queryParams' => array_filter($data)]);

        $query = Worksheet::query()
            ->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type'])
            ->filter($filter);

        $result = $query->get();

        return $result;
    }

    /**
     * Закрыть РЛ
     * @param Worksheet $worksheet
     */
    public function close(Worksheet $worksheet) : void
    {
        if($worksheet->status_id != 'check')
            throw new \Exception('Команда не может быть выполнена. Закрыть можно только рабочий лист, находящийся на проверке');

        if(!in_array($worksheet->last_action->task->slug, ['confirm','abort']))
            throw new \Exception('Последнее действие в рабочем листе не подразумевает его закрытие. Создайте закрывающее действие.');

        $worksheet->status_id = $worksheet->last_action->task->slug;
        $worksheet->close_at = now();
        $worksheet->inspector_id = auth()->user()->id;
        $worksheet->save();

        Comment::add($worksheet->last_action, 'close');
    }

    /**
     * Вернуть закрытый РЛ в работу
     * @param Worksheet $worksheet
     */
    public function revert(Worksheet $worksheet) : void
    {
        if(in_array($worksheet->status_id, ['work']))
            throw new \Exception('Рабочий лист в работе, незачем его возвращать в работу.');

        $worksheet->status_id = 'work';
        $worksheet->inspector_id = null;
        $worksheet->close_at = null;
        $worksheet->save();

        $worksheet->last_action->fill([
            'task_id' => \App\Models\Task::where('slug','control')->first()->id,
            'worksheet_id' => $worksheet->id,
            'begin_at' => now(),
            'end_at' => now()->addMinutes(30),
            'author_id' => auth()->user()->id,
            'status' => 'work'
        ])->save();

        Comment::add($worksheet->last_action, 'revert');
    }
}
