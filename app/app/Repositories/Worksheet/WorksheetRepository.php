<?php

namespace App\Repositories\Worksheet;

use App\Models\SubAction;
use App\Models\Task;
use App\Models\Worksheet;
use App\Models\Trafic;
use App\Repositories\Client\ClientRepository;
use App\Services\Comment\Comment;
use App\Services\Worksheet\ActionSave;
use stdClass;

/**
 * РЕПОЗИТОРИЙ РАБОЧЕГО ЛИСТА
 * - СОЗДАТЬ РЛ ИЗ ТРАФИКА
 * - [private] КОНФИГУРИРОВАНИЕ ФИЛЬТРА ПО ПАРАМЕТРАМ
 * - СПИСОК РАБОЧИХ ЛИСТОВ В ВИДЕ ПАГИНАЦИИ
 * - СПИСОК РАБОЧИХ ЛИСТОВ В ВИДЕ КОЛЛЕКЦИИ
 * - КОЛ-ВО РАБОЧИХ ЛИСТОВ, ПОДХОДЯЩИХ ПОД ПАРАМЕТРЫ ФИЛЬТРАЦИИ
 * - ПОЛУЧИТЬ КОЛ-ВО РАБОЧИХ ЛИСТОВ НАХОДЯЩИХСЯ В РАБОТЕ
 * - ПОЛУЧИТЬ СПИСОК РЛ ДЛЯ ЖУРНАЛА ЗАДАЧ
 * - ЗАКРЫТЬ РЛ
 * - ВЕРНУТЬ РЛ В РАБОТУ
 *
 * 11-09-2023
 *
 */
class WorksheetRepository
{
    /**
     * СОЗДАТЬ РЛ ИЗ ТРАФИКА
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



    /**
     * КОНФИГУРИРОВАНИЕ ФИЛЬТРА ПО ПАРАМЕТРАМ
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder
     */
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



    /**
     * СПИСОК РАБОЧИХ ЛИСТОВ В ВИДЕ ПАГИНАЦИИ
     * @param array $data ПАРАМЕТРЫ ДЛЯ ФИЛЬТРАЦИИ
     * @param int $paginate
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function paginate(array $data, $paginate = 20) : \Illuminate\Contracts\Pagination\Paginator
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



    /**
     * СПИСОК РАБОЧИХ ЛИСТОВ В ВИДЕ КОЛЛЕКЦИИ, АККУРАТНО С ФИЛЬТРОМ, ИНАЧЕ МОЖЕТ ВЕРНУТЬ ВСЕ А ЭТО МНОГО
     * @param array $data ПАРАМЕТРЫ ДЛЯ ФИЛЬТРАЦИИ
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(array $data) : \Illuminate\Database\Eloquent\Collection
    {
        $query = Worksheet::query()->select('worksheets.*');

        $query->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type']);

        $this->filter($query,$data);

        $query->groupBy('worksheets.id')->groupBy('worksheet_actions.begin_at');

        $result = $query->orderBy('worksheet_actions.begin_at')->get();

        return $result;
    }



    /**
     * КОЛ-ВО РАБОЧИХ ЛИСТОВ, ПОДХОДЯЩИХ ПОД ПАРАМЕТРЫ ФИЛЬТРАЦИИ
     * @param array $data ПАРАМЕТРЫ ДЛЯ ФИЛЬТРАЦИИ
     * @return int
     */
    public function counter(array $data) : int
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



    /**
     * ПОЛУЧИТЬ КОЛ-ВО РАБОЧИХ ЛИСТОВ НАХОДЯЩИХСЯ В РАБОТЕ + ПОДХОДЯЩИХ ПОД ПАРАМЕТРЫ ФИЛЬТРАЦИИ
     * @param $data ПАРАМЕТРЫ ДЛЯ ФИЛЬТРАЦИИ
     * @return int
     */
    public function workingCount($data) : int
    {
        $data['status_ids'] = ['work'];
        $count = $this->counter($data);
        return $count;
    }



    /**
     * ПОЛУЧИТЬ СПИСОК РЛ ДЛЯ ЖУРНАЛА ЗАДАЧ
     * @param array $data ПАРАМЕТРЫ ДЛЯ ФИЛЬТРАЦИИ
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWorksheetsForTaskList(array $data) //: \Illuminate\Database\Eloquent\Collection
    {
        $filter = app()->make(\App\Http\Filters\WorksheetListFilter::class, ['queryParams' => array_filter($data)]);

        $query = Worksheet::query()
            ->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type'])
            ->filter($filter);

        $result = $query->get()->map(fn($item) => (object)[
            'id'                => $item->id,
            'type'              => $item->last_action->task->name,
            'status'            => $item->last_action->statusMsg(),
            'client'            => $item->client->fullNameOrType,
            'begin_at'          => $item->last_action->begin_at->format('d.m.Y (H:i)'),
            'end_at'            => $item->last_action->end_at->format('d.m.Y (H:i)'),
            'appeal'            => $item->appeal->name,
            'comment'           => $item->last_action->last_user_comment->text,
            'author'            => $item->author->cut_name,
            'managers'          => $item->executors->map(fn($executor) => $executor->cut_name)->toArray(),
            'worksheet_status'  => $item->status->name,
            'salon'             => $item->company->name,
            'structure'         => isset($item->structure) ? $item->structure->name : '',
            'sub_action_id'     => '',
            'reporters'         => [],
        ])->toArray();

        return $result;
    }

    public function getSubActionForTaskList(array $data)
    {
        $filterSubAction = app()->make(\App\Http\Filters\WorksheetSubActionFilter::class, ['queryParams' => array_filter($data)]);
        $querySubAction = SubAction::query()
            ->with(['worksheet', 'executors','reporters'])
            ->filter($filterSubAction);

        $resultSubAction = $querySubAction->get();

        $resultSubAction = $querySubAction->get()->map(fn($item) => (object)[
            'id'                => $item->worksheet_id,
            'type'              => '',
            'status'            => SubAction::STATUSES[$item->status],
            'client'            => $item->title,
            'begin_at'          => $item->created_at->format('d.m.Y (H:i)'),
            'end_at'            => $item->created_at->addMinutes($item->duration)->format('d.m.Y (H:i)'),
            'appeal'            => '',
            'comment'           => '',
            'author'            => $item->author->cut_name,
            'managers'          => $item->executors->map(fn($executor) => $executor->cut_name)->toArray(),
            'worksheet_status'  => '',
            'salon'             => '',
            'structure'         => '',
            'sub_action_id'     => $item->id,
            'reporters'         => $item->reporters->map(fn($reporter) => $reporter->cut_name)->toArray()
        ])->toArray();

        return $resultSubAction;
    }



    /**
     * ЗАКРЫТЬ РЛ
     * @param Worksheet $worksheet
     * @return void
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
     * ВЕРНУТЬ РЛ В РАБОТУ
     * @param Worksheet $worksheet
     * @return void
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

    public function setDefaultStatus(&$data)
    {

    }
}
