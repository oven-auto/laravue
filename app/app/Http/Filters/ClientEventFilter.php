<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ClientEventFilter extends AbstractFilter
{
    public const CLIENT_ID = 'client_id';
    public const TYPE_ID = 'type_id';
    public const EXECUTOR_IDS = 'executor_ids';
    public const STATUS = 'status_id';
    public const IDS = 'ids';
    public const TITLE = 'title';
    public const CONTROL_START = 'control_begin';
    public const CONTROL_END = 'control_end';
    public const PROCESSED_START = 'processed_begin';
    public const PROCESSED_END = 'processed_end';
    public const PROCESSED_CONTROLL = 'processed_control';
    public const INPUT = 'input';
    public const COMPLETER_IDS = 'completer_ids';
    public const INTERVAL = 'interval';
    public const GROUP_ID = 'group_id';
    public const AUTHOR_ID = 'author_id';
    public const PERSONAL    = 'personal';
    public const CONTROL_DATE = 'control_date';
    public const SHOW       = 'show';
    public const DATE_FOR_CLOSING = 'date_for_closing';

    protected function getCallbacks(): array
    {
        return [
            self::CLIENT_ID             => [$this, 'clientId'],
            self::TYPE_ID               => [$this, 'typeId'],
            self::EXECUTOR_IDS          => [$this, 'executorIds'],
            self::STATUS                => [$this, 'status'],
            self::IDS                   => [$this, 'ids'],
            self::TITLE                 => [$this, 'title'],
            self::CONTROL_START         => [$this, 'controlStart'],
            self::CONTROL_END           => [$this, 'controlEnd'],
            self::PROCESSED_START       => [$this, 'processedStart'],
            self::PROCESSED_END         => [$this, 'processedEnd'],
            self::INPUT                 => [$this, 'input'],
            self::COMPLETER_IDS         => [$this, 'completerIds'],
            self::INTERVAL              => [$this, 'interval'],
            self::GROUP_ID              => [$this, 'groupId'],
            self::AUTHOR_ID             => [$this, 'authorId'],
            self::PERSONAL              => [$this, 'personal'],
            self::PROCESSED_CONTROLL    => [$this, 'processedControll'],
            self::CONTROL_DATE          => [$this, 'controlDate'],
            self::SHOW                  => [$this, 'show'],
            self::DATE_FOR_CLOSING      => [$this, 'dateForClosing'],
        ];
    }

    public function dateForClosing(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        $builder->whereDate('client_event_statuses.date_at', '=', $date);
    }

    public function controlDate(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        if($date <= now())
            $builder->whereDate('client_event_statuses.date_at', '<=', $date);
        else
            $builder->whereDate('client_event_statuses.date_at', '=', $date);
    }

    public function show(Builder $builder, $value)
    {
        if($value == 'closing')
        {
            $this->delWhere($builder, 'client_event_statuses.confirm');
            $builder->where('client_event_statuses.confirm','processed');
        }
        elseif($value == 'opening')
        {
            $this->delWhere($builder, 'client_event_statuses.confirm');
            $builder->where('client_event_statuses.confirm','waiting');
        }

    }

    public function processedControll(Builder $builder, $arr)
    {
        $this->delWhere($builder, 'client_event_statuses.confirm');
        $builder->where('client_event_statuses.confirm', 'processed');
        $builder->whereBetween('client_event_statuses.processed_at', [$this->formatDate($arr[0]), $this->formatDate($arr[1])]);
    }

    public function authorId(Builder $builder, $value)
    {
        if(!$this->checkJoin($builder, 'client_events'))
            $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');
        $builder->where('client_events.author_id', $value);
    }


    // private function checkJoin(Builder $builder, $table)
    // {
    //     $res = collect($builder->getQuery()->joins)->pluck('table')->contains($table);
    //     return $res;
    // }

    public function delWhere(Builder $builder, $where)
    {
        foreach($builder->getQuery()->wheres as $key => &$itemWhere) {
            if(isset($itemWhere['column']))
                if($itemWhere['column'] == $where) {
                    unset($builder->getQuery()->bindings['where'][$key]);
                    unset($builder->getQuery()->wheres[$key]);
                }
        }
    }

    public function delWhereFromQuery(\Illuminate\Database\Query\Builder $builder, $where)
    {
        foreach($builder->wheres as $key => &$itemWhere) {
            if(isset($itemWhere['column']))
                if($itemWhere['column'] == $where) {
                    unset($builder->bindings['where'][$key]);
                    unset($builder->wheres[$key]);
                }
        }
    }

    public function personal(Builder $builder, $value)
    {
        $this->delWhere($builder,'client_events.personal');

        foreach($builder->getQuery()->wheres as $key => &$itemWhere)
            if(isset($itemWhere['query']) )
                foreach($itemWhere['query']->wheres as $subQ)
                    if(isset($subQ['column']) && $subQ['column'] == 'client_events.personal')
                        $itemWhere['boolean'] = 'and';
    }

    public function completerIds(Builder $builder, $value){
        $this->delWhere($builder, 'client_event_statuses.confirm');
        $builder->where('client_event_statuses.confirm', 'processed');
        $builder->whereIn('client_event_statuses.author_id', $value);
    }

    public function interval(Builder $builder, $value)
    {
        switch ($value){
            case 'today':
                $builder->where(function($builder) {
                    $builder->whereDate('client_event_statuses.date_at','=', now());
                    $builder->orWhere(function($builder){
                        $builder->where('client_event_statuses.date_at','<',date('Y-m-d'));
                    });
                });
                break;
            case 'tomorrow':
                $builder->whereDate('client_event_statuses.date_at','=',  now()->addDay());
                break;
            case 'finish_today':
                $this->delWhere($builder, 'client_event_statuses.confirm');
                $builder->where('client_event_statuses.confirm','processed');
                $builder->whereDate('client_event_statuses.processed_at','=',  now());
                break;
        }
    }

    public function clientId(Builder $builder, Int $value)
    {
        if(!$this->checkJoin($builder, 'client_events'))
            $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');
        $builder->where('client_events.client_id', $value);
    }

    public function groupId(Builder $builder, Int $value)
    {
        if(!$this->checkJoin($builder, 'client_events'))
            $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');
        $builder->where('client_events.group_id', $value);
    }

    public function typeId(Builder $builder, Int $value)
    {
        if(!$this->checkJoin($builder, 'client_events'))
            $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');
        $builder->where('client_events.type_id', $value);
    }

    public function executorIds(Builder $builder, Array $value)
    {
        if(!$this->checkJoin($builder, 'client_event_executors'))
            $builder->leftJoin('client_event_executors', 'client_event_executors.event_id','client_event_statuses.event_id');
        $builder->whereIn('client_event_executors.executor_id', $value);
        $builder->groupBy('client_event_executors.executor_id');
    }

    public function status(Builder $builder, String $value)
    {
        $status = \App\Models\ClientEventStatusDescription::find($value)->confirm;

        if($status)
            $builder->where('client_event_statuses.confirm', $status);
    }

    public function ids(Builder $builder, String $value)
    {
        // if(!$this->checkJoin($builder, 'client_events'))
        //     $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');
        $builder->whereIn('client_event_statuses.id', explode(',',$value));
    }

    public function title(Builder $builder, String $value)
    {
        if(!$this->checkJoin($builder, 'client_events'))
            $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');
        $builder->where('client_events.title', 'LIKE', "%{$value}%");
    }

    public function controlStart(Builder $builder, String $value)
    {
        $builder->whereDate('client_event_statuses.date_at','>=', $this->formatDate($value));
    }

    public function controlEnd(Builder $builder, String $value)
    {
        $builder->whereDate('client_event_statuses.date_at','<=', $this->formatDate($value));
    }

    /**
     * ЗАВЕРШЕНЫЕ С
     */
    public function processedStart(Builder $builder, String $value)
    {
        $this->delWhere($builder, 'client_event_statuses.confirm');
        $builder->where('client_event_statuses.confirm', 'processed');
        $builder->whereDate('client_event_statuses.processed_at','>=', $this->formatDate($value));
    }

    /**
     * ЗАВЕРШЕНЫЕ ДО
     */
    public function processedEnd(Builder $builder, String $value)
    {
        $this->delWhere($builder, 'client_event_statuses.confirm');
        $builder->where('client_event_statuses.confirm', 'processed');
        $builder->whereDate('client_event_statuses.processed_at','<=', $this->formatDate($value));
    }

    public function input(Builder $builder, $value)
    {
        if(!$this->checkJoin($builder, 'client_events'))
            $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');

        if(!$this->checkJoin($builder, 'clients'))
            $builder->leftJoin('clients', 'clients.id','client_events.client_id');

        $builder->leftJoin('client_phones', 'clients.id','client_phones.client_id');
        $builder->leftJoin('client_inns', 'clients.id','client_inns.client_id');

        $builder->where(function ($builder) use ($value) {
            $builder->orWhere('clients.firstname', 'LIKE', "%{$value}%");
            $builder->orWhere('clients.lastname', 'LIKE', "%{$value}%");
            $builder->orWhere('clients.company_name', 'LIKE', "%{$value}%");
            $builder->orWhere('client_phones.phone', 'LIKE', "%{$value}%");
            $builder->orWhere('client_inns.number', 'LIKE', "%{$value}%");
            $builder->orWhere('client_event_statuses.id', $value);
        });

        $builder->groupBy('client_phones.phone');
        $builder->groupBy('client_inns.id');
    }
}
