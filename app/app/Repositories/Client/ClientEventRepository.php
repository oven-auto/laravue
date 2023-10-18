<?php

namespace App\Repositories\Client;
use App\Models\ClientEvent;
use App\Models\ClientEventExecutor;
use App\Models\ClientEventStatus;

class ClientEventRepository
{
    private $authId;
    private $event;
    private $data;
    private $files;

    public function save(ClientEvent $event, Array $data, $files = [])
    {
        $this->event = $event;
        $this->data = $data;
        $this->files = $files;
        $this->authId = auth()->user()->id;
        $this->saveFasade();
        $this->event->fresh();
        $this->saveFile();
        $this->event = null;
        $this->data = null;
    }

    private function checkOnOwner()
    {

    }

    public function saveFile()
    {
        $arr = [];
        if(isset($this->files))
            foreach($this->files as $file)
                $this->event->files()->create([
                    'event_id' => $this->event->id,
                    'author_id' => auth()->user()->id,
                    'file' => \App\Services\Download\ClientEventFileLoad::download($this->event->id, $file)
                ]);
    }

    public function saveFasade()
    {
        $date_at = (string)$this->data['date_at'];

        $arr['date_at'] = date('Y-m-d',\strtotime($date_at));

        if(isset($this->data['begin_time']))
            $arr['begin_time'] = $this->data['begin_time'];

        if(isset($this->data['end_time']))
            $arr['end_time'] = $this->data['end_time'];

        $this->saveMain();

        if($this->event->wasRecentlyCreated)
            $this->event->statuses()->create($arr);
        else
            $this->event->lastStatus->fill($arr)->save();
        $this->createComments();
        $this->syncExecutors();
    }

    public function saveMain()
    {
        if(!$this->event->id || $this->event->author_id === $this->authId) {
            $date_at = (string)$this->data['date_at'];
            $arr = [
                'client_id'     => $this->event->client_id ? $this->event->client_id : $this->data['client_id'],
                'author_id'     => $this->event->auth_id ? $this->event->auth_id : $this->authId,
                'group_id'      => isset($this->data['group_id']) ? $this->data['group_id'] : NULL,
                'type_id'       => isset($this->data['type_id']) ? $this->data['type_id'] : '',
                'title'         => $this->data['title'],
                'resolve'       => ($this->data['resolve']!="false") ? (($this->data['resolve']) ? 1 : 0) : 0,
                'personal'      => isset($this->data['personal']) ? $this->data['personal'] : 0,
            ];
            $this->event->fill($arr)->save();
        }
    }

    private function createComments()
    {
        if($this->data['text'] && $this->data['text'])
            $this->event->comments()->create([
                'text' => $this->data['text'],
                'author_id' => $this->authId,
                'client_event_status_id' => $this->event->lastStatus->id
            ]);
    }

    private function syncExecutors()
    {
        if(isset($this->data['executors']) && is_array($this->data['executors']) && count($this->data['executors']))
        {
            $this->data['executors'][] = $this->event->author_id;
            $this->data['executors'] = array_unique($this->data['executors']);
            foreach($this->data['executors'] as $item)
                if(!$this->event->executors->contains('id', $item))
                {
                    $this->event->executors()->attach(['executor_id' => $item]);
                    $this->event->lastComment()->create([
                        'author_id' => auth()->user()->id,
                        'text' => 'Добавлен ответственный '.\App\Models\User::find($item)->cut_name,
                        'event_id' => $this->event->id,
                        'client_event_status_id' => $this->event->lastStatus->id
                    ]);
                }
            foreach($this->event->executors as $item)
                if(!in_array($item->id, $this->data['executors']))
                {
                    $this->event->executors()->detach(['executor_id' => $item->id]);
                    $this->event->lastComment()->create([
                        'author_id' => auth()->user()->id,
                        'text' => 'Удален ответственный '.$item->cut_name,
                        'event_id' => $this->event->id,
                        'client_event_status_id' => $this->event->lastStatus->id
                    ]);
                }
        }
    }

    public function getAllInGroupByClientId(Int $clientId)
    {
        $res = ClientEventStatus::select('client_event_statuses.*')
            ->leftJoin('client_events','client_events.id','client_event_statuses.event_id')
            ->where('client_events.client_id', $clientId)
            ->where('client_event_statuses.confirm', 'waiting')
            ->with(['event'])
            ->orderBy('client_event_statuses.date_at')
            ->get();
        return $res;
    }

    public function filter($query, Array $data)
    {
        $this->closingOrWorking($data, $query);

        $query->leftJoin('client_events', 'client_events.id','client_event_statuses.event_id')
            ->leftJoin('client_event_executors','client_event_executors.event_id','client_events.id')
            ->where('client_events.personal',0);

        $filter = app()->make(\App\Http\Filters\ClientEventFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);
    }

    public function counter(Array $data) : int
    {
        $query = ClientEventStatus::query();

        $subQuery = ClientEventStatus::query();

        $this->filter($subQuery,$data);

        $subQuery->OnlyTableData()->ListOrder();

        $query->rightJoinSub($subQuery, 'subQuery', function($join){
            $join->on('subQuery.id','=','client_event_statuses.id');
        });
        $result = $query->count();

        return $result;
    }

    public function paginate(Array $data, $paginate = 15)
    {
        $query = ClientEventStatus::query();

        $this->filter($query,$data);

        $query->OnlyTableData()->WithEventAndTrafic()->ListOrder();

        $result = $query->simplePaginate($paginate);

        return $result;
    }

    public function get(Array $data)
    {
        $query = ClientEventStatus::query();

        $query->leftJoin('client_events', 'client_events.id','client_event_statuses.event_id');

        $query->leftJoin('client_event_executors','client_event_executors.event_id','client_events.id');

        $filter = app()->make(\App\Http\Filters\ClientEventFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $query->OnlyTableData()->WithEventAndTrafic()->ListOrder();

        $result = $query->get();

        return $result;
    }

    private function closingOrWorking(&$data, $query)
    {
        $val = isset($data['processed_begin']) || isset($data['processed_end']);
        $isControlDates = isset($data['control_begin']) || isset($data['control_end']);

        if($isControlDates && isset($data['interval']))
            throw new \Exception('Невозможно поставить условие (сегодня, завтра, завершено сегодня) на интервал период контроля.');

        if($val)
        {
            //$data['processed_control'] = [ $data['processed_begin'], $data['processed_end'] ];
            //unset($data['processed_begin'], $data['processed_end']);
            if(isset($data['interval']))
                throw new \Exception('Невозможно поставить условие (сегодня, завтра, завершено сегодня) на интервал период завершения.');
        }
        elseif(isset($data['interval']) && $data['interval'] == 'finish_today')
        {

        }
        else
        {
            $query->where(function($query){
                $query->where('client_events.personal', 1)
                    ->where('client_event_executors.executor_id', auth()->user()->id)
                    ->where('client_event_statuses.confirm', 'waiting');
            });

            $query->orWhere('client_event_statuses.confirm', 'waiting');
        }
    }

}

