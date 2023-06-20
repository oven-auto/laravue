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
        //$this->data['executors'] = explode(',', $this->data['executors']);
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
        {
            foreach($this->files as $file)
                $this->event->files()->create([
                    'event_id' => $this->event->id,
                    'author_id' => auth()->user()->id,
                    'file' => \App\Services\Download\ClientEventFileLoad::download($this->event->id, $file)
                ]);
        }
    }

    public function saveFasade()
    {
        $date_at = (string)$this->data['date_at'];

        $this->saveMain();
        $this->syncExecutors();
        if($this->event->wasRecentlyCreated)
            $this->event->statuses()->create([
                'date_at'       => date('Y-m-d',\strtotime($date_at)),
            ]);
        else
            $this->event->lastStatus->fill(['date_at' => $date_at])->save();
        $this->createComments();
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
        if(isset($this->data['executors']) && is_array($this->data['executors']) && count($this->data['executors'])) {
            ClientEventExecutor::where('event_id', $this->event->id)->delete();
            $this->data['executors'][] = $this->event->author_id;
            //dump($this->data['executors']);
            foreach($this->data['executors'] as $item)
                $this->event->executors()->attach([
                    //'event_id' => $this->event->id,
                    'executor_id' => $item
                ]);
        }
        // if(isset($this->data['executors'])) {
        //     foreach($this->data['executors'] as $itemExecutorId) {
        //         dump($itemExecutorId);
        //         if(!$executorArr->contains('id', $itemExecutorId))
        //             dump('----'.$itemExecutorId);
        //             $this->event->executors()->attach($itemExecutorId);
        //     }
        // }
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
        $filter = app()->make(\App\Http\Filters\ClientEventFilter::class, ['queryParams' => array_filter($data)]);
        $query->filter($filter);
    }

    public function counter(Array $data) : int
    {
        $query = ClientEventStatus::query();

        $query->where('client_event_statuses.confirm', 'waiting');

        $this->filter($query,$data);

        $result = $query->count();

        return $result;
    }

    public function paginate(Array $data, $paginate = 15)
    {
        $query = ClientEventStatus::query();

        $query->where('client_event_statuses.confirm', 'waiting');

        $this->filter($query,$data);

        $query->onlyMy()->WithEventAndTrafic()->ListOrder();

        $result = $query->simplePaginate($paginate);

        return $result;
    }
}
