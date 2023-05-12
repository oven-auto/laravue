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
    public const INPUT = 'input';
    public const COMPLETER_IDS = 'completer_ids';
    public const INTERVAL = 'interval';

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
        ];
    }

    private function checkJoin(Builder $builder, $table)
    {
        $res = collect($builder->getQuery()->joins)->pluck('table')->contains($table);
        return $res;
    }

    public function completerIds(Builder $builder, $value){
        $builder->whereIn('client_event_statuses.author_id', $value);
    }

    public function interval(Builder $builder, $value)
    {
        switch ($value){
            case 'today':
                $builder->whereDate('client_event_statuses.date_at','=', now());
                break;
            case 'tomorrow':
                $builder->whereDate('client_event_statuses.date_at','=',  now()->addDay());
                break;
        }
    }

    public function clientId(Builder $builder, Int $value)
    {
        if(!$this->checkJoin($builder, 'client_events'))
            $builder->leftJoin('client_events', 'client_event_statuses.event_id','client_events.id');
        $builder->where('client_events.client_id', $value);
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

    public function processedStart(Builder $builder, String $value)
    {
        $builder->whereDate('client_event_statuses.processed_at','>=', $this->formatDate($value));
        //$builder->where('')
    }

    public function processedEnd(Builder $builder, String $value)
    {
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
        });

        $builder->groupBy('client_phones.phone');
        $builder->groupBy('client_inns.id');
    }
}
