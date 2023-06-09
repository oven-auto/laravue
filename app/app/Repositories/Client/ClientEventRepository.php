<?php

namespace App\Repositories\Client;
use App\Models\ClientEvent;
use App\Models\ClientEventStatus;

class ClientEventRepository
{
    private $authId;
    private $event;
    private $data;

    public function save(ClientEvent $event, Array $data)
    {
        $this->event = $event;
        $this->data = $data;
        $this->authId = auth()->user()->id;
        $this->saveFasade();
        $this->event->fresh();
        $this->event = null;
        $this->data = null;
    }

    private function checkOnOwner()
    {

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
                'resolve'       => isset($this->data['resolve']) ? (($this->data['resolve']) ? 1 : 0) : 0,
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
        $arr[] = $this->authId;
        $executorArr = [];

        if(isset($this->data['executors'])) {
            foreach($this->data['executors'] as $itemExecutorId) {
                if($itemExecutorId)
                    $executorArr[] = $itemExecutorId;
            }
            $arr = array_merge($arr,$executorArr);
            $arr = array_unique($arr);
        }
        $this->event->executors()->sync($arr);
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
