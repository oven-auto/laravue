<?php

namespace App\Http\Filters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

Class WorksheetFilter extends AbstractFilter
{
    //private $completedDateArray = ['begin_at' => '', 'end_at' => ''];
    public const INPUT = 'input';
    public const TASK_ID = 'task_id';//+++
    public const CLIENT_TYPE = 'client_type'; //+++
    public const APPEAL_IDS = 'appeal_ids';//+++
    public const SALON_IDS = 'salon_ids';//+++
    public const STRUCTURE_IDS = 'structure_ids';//+++
    public const EXECUTOR_IDS = 'executor_ids';//+++
    public const STATUS_IDS = 'status_ids';//+++
    public const AUTHOR_ID = 'author_id';
    public const IDS = 'ids';//+++
    public const INTERVAL = 'interval';
    public const BEGIN_AT = 'begin_at';//+++
    public const END_AT = 'end_at';//+++
    // public const ACTION_STATUS = 'action_status';//---
    // public const COMPLETED_BEGIN = 'completed_begin';//---
    // public const COMPLETED_END = 'completed_end';//---
    // public const COMPLETED_INTERVAL = 'completed_interval';//---
    // public const ABORTED_OR_CONFIRMED = 'abortedOrConfirmed';//---
    public const CONTROL_DATE = 'control_date';
    public const SHOW    = 'show';
    public const DATE_FOR_CLOSING = 'date_for_closing';
    //public const CLOSED = 'closed';
    public const CREATED_BEGIN = 'register_begin';
    public const CREATED_END = 'register_end';
    public const CREATED_MONTH = 'created_month';
    public const CLOSED_MONTH = 'closed_month';
    public const CLOSED_BEGIN = 'closed_date';
    public const CLOSED_END = 'closed_end';

    protected function getCallbacks(): array
    {
        return [
            self::INPUT                 => [$this, 'input'],
            self::TASK_ID               => [$this, 'taskId'],
            self::CLIENT_TYPE           => [$this, 'clientType'],
            self::APPEAL_IDS            => [$this, 'appealIds'],
            self::SALON_IDS             => [$this, 'salonIds'],
            self::STRUCTURE_IDS         => [$this, 'structureIds'],
            self::EXECUTOR_IDS          => [$this, 'executorIds'],
            self::STATUS_IDS            => [$this, 'statusIds'],
            self::AUTHOR_ID             => [$this, 'authorId'],
            self::IDS                   => [$this, 'ids'],
            self::INTERVAL              => [$this, 'interval'],
            self::BEGIN_AT              => [$this, 'beginAt'],
            self::END_AT                => [$this, 'endAt'],
            // self::ACTION_STATUS         => [$this, 'actionStatus'],
            // self::COMPLETED_BEGIN       => [$this, 'completedBegin'],
            // self::COMPLETED_END         => [$this, 'completedEnd'],
            // self::COMPLETED_INTERVAL    => [$this, 'completedInterval'],
            // self::ABORTED_OR_CONFIRMED  => [$this, 'abortedOrConfirmed'],
            self::CONTROL_DATE          => [$this, 'controlDate'],
            self::SHOW                  => [$this, 'show'],
            self::DATE_FOR_CLOSING      => [$this, 'dateForClosing'],
            //self::CLOSED                => [$this, 'closed'],
            self::CREATED_BEGIN         => [$this, 'createdBegin'],
            self::CREATED_END           => [$this, 'createdEnd'],
            self::CLOSED_BEGIN          => [$this, 'closedBegin'],
            self::CLOSED_END            => [$this, 'closedEnd'],
            self::CREATED_MONTH         => [$this, 'createdMonth'],
            self::CLOSED_MONTH          => [$this, 'closedMonth'],
        ];
    }

    public function createdMonth(Builder $builder, $value)
    {
        $date = $this->formatDate($value);
        $carbon =  Carbon::createFromFormat('Y-m-d', $date);

        $builder->where(function($query) use ($carbon) {
            $query
                ->whereYear('worksheets.created_at', '=', $carbon->year)
                ->whereMonth('worksheets.created_at', '=', $carbon->month);
        });
    }

    public function closedMonth(Builder $builder, $value)
    {
        $date = $this->formatDate($value);
        $carbon =  Carbon::createFromFormat('Y-m-d', $date);

        $builder->where(function($query) use ($carbon) {
            $query
                ->whereIn('worksheet_actions.status', ['confirm','abort'])
                ->whereYear('worksheet_actions.created_at', '=', $carbon->year)
                ->whereMonth('worksheet_actions.created_at', '=', $carbon->month);
        });
    }

    public function createdBegin(Builder $builder, $value)
    {
        $builder->whereDate('worksheets.created_at', '>=', $this->formatDate($value));
    }

    public function createdEnd(Builder $builder, $value)
    {
        $builder->whereDate('worksheets.created_at', '<=', $this->formatDate($value));
    }

    public function closedBegin(Builder $builder, $value)
    {
        $builder->whereIn('worksheet_actions.status', ['confirm','abort'])
            ->whereDate('worksheet_actions.created_at', '>=', $this->formatDate($value));
    }

    public function closedEnd(Builder $builder, $value)
    {
        $builder->whereIn('worksheet_actions.status', ['confirm','abort'])
            ->whereDate('worksheet_actions.created_at', '<=', $this->formatDate($value));
    }

    public function dateForClosing(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        $builder->whereDate('worksheet_actions.begin_at', '=', $date);
    }

    public function show(Builder $builder, $value)
    {
        if($value == 'opening')
            $builder->whereIn('worksheets.status_id', ['work']);

        else if($value == 'closing')
            $builder->whereIn('worksheets.status_id', ['confirm','abort','check']);
    }

    public function controlDate(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        if(now()>=$date)
            $builder->whereDate('worksheet_actions.begin_at', '<=', $date);

        else
            $builder->whereDate('worksheet_actions.begin_at', '=', $date);
    }

    public function delWhere(Builder $builder, $where)
    {
        foreach($builder->getQuery()->wheres as $key => &$itemWhere) {
            if(isset($itemWhere['column']) && $itemWhere['column'] == $where) {
                unset($builder->getQuery()->bindings['where'][$key]);
                unset($builder->getQuery()->wheres[$key]);
            }
        }
    }

    // public function abortedOrConfirmed(Builder $builder, $value)
    // {
    //     $builder->whereBetween('worksheets.close_at', [$this->formatDate($value[0]), $this->formatDate($value[1])]);
    // }

    // public function completedInterval(Builder $builder, $value)
    // {
    //     switch($value) {
    //         case 'today':
    //             $builder->whereDate('worksheets.close_at', now());
    //             break;
    //         case 'yesterday':
    //             $builder->whereDate('worksheets.close_at', now()->subDay());
    //             break;
    //         case 'week':
    //             $builder->whereBetween('worksheets.close_at', [now()->startOfWeek(), now()->endOfWeek()]);
    //             break;
    //         case 'month':
    //             $builder->where(function($query)  {
    //                 $query
    //                     ->whereYear('worksheets.close_at', '=', now()->year)
    //                     ->whereMonth('worksheets.close_at', '=', now()->month);
    //             });
    //             break;
    //         case 'year':
    //             $builder->whereYear('worksheets.close_at', now()->year);
    //             break;
    //     }
    // }

    // public function completedBegin(Builder $builder, $value)
    // {

    //     $builder->orWhereDate('worksheets.close_at','>=', $this->formatDate($value));
    // }

    // public function completedEnd(Builder $builder, $value)
    // {

    //     $builder->orWhereDate('worksheets.close_at','<=', $this->formatDate($value));
    // }

    // public function actionStatus(Builder $builder, $value)
    // {
    //     $builder->where('worksheet_actions.status', $value);
    // }




    public function beginAt(Builder $builder, $value)
    {
        $builder->whereDate('worksheet_actions.begin_at','>=', $this->formatDate($value));
    }

    public function endAt(Builder $builder, $value)
    {
        $builder->whereDate('worksheet_actions.begin_at','<=', $this->formatDate($value));
    }

    public function interval(Builder $builder, $value)
    {
        switch ($value) {
            case 'month':
                $builder->where(function($query)  {
                    $query
                        ->whereYear('worksheet_actions.begin_at', '=', now()->year)
                        ->whereMonth('worksheet_actions.begin_at', '=', now()->month);
                });
                break;
            case 'week':
                $builder->whereBetween('worksheet_actions.begin_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;
            case 'today':
                $builder->whereDate('worksheet_actions.begin_at', now());
                break;
            case 'tomorrow':
                $builder->whereDate('worksheet_actions.begin_at', now()->nextDay());
                break;
            default:
                break;
        }
    }

    public function ids(Builder $builder, $value)
    {
        $builder->whereIn('worksheets.id', $value);
    }

    public function authorId(Builder $builder, $value)
    {
        $builder->where('worksheets.author_id', $value);
    }

    public function statusIds(Builder $builder, $value)
    {
        $builder->whereIn('worksheets.status_id',$value);
    }

    public function executorIds(Builder $builder, $value)
    {
        $builder->leftJoin('worksheet_executors','worksheet_executors.worksheet_id','worksheets.id');

        $builder->where(function($query) use ($value){
            $query->whereIn('worksheet_executors.user_id', $value);
        });
    }

    public function salonIds(Builder $builder, $value)
    {
        $builder->whereIn('worksheets.company_id', $value);
    }

    public function structureIds(Builder $builder, $value)
    {
        $builder->whereIn('worksheets.structure_id', $value);
    }

    public function appealIds(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->whereIn('worksheets.appeal_id', $value);
        elseif(is_numeric($value) || is_string($value))
            $builder->where('worksheets.appeal_id', $value);
    }

    public function clientType(Builder $builder, $value)
    {
        if(!$this->checkJoin($builder, 'clients'))
            $builder->leftJoin('clients', 'clients.id','worksheets.client_id');
        $builder->where('clients.client_type_id', $value);
    }

    public function taskId(Builder $builder, $value)
    {
        $builder->where('worksheet_actions.task_id', $value);
    }

    public function input(Builder $builder, $value)
    {
        if(!$this->checkJoin($builder, 'clients'))
            $builder->leftJoin('clients', 'clients.id','worksheets.client_id');

        if(!$this->checkJoin($builder, 'client_phones'))
            $builder->leftJoin('client_phones', 'client_phones.client_id','clients.id');

        if(!$this->checkJoin($builder, 'client_inns'))
            $builder->leftJoin('client_inns', 'client_inns.client_id','clients.id');

        if(!$this->checkJoin($builder, 'client_emails'))
            $builder->leftJoin('client_emails', 'client_emails.client_id','clients.id');

        $builder->where(function($query) use ($value)
        {
            if(preg_match('/\s/',$value))
            { //есть пробелы
                $query->orWhere(function($query) use ($value)
                {
                    $data = explode(' ', $value);
                    list($param1, $param2) = $data;
                    $query->orWhere(function($query) use ($param1, $param2){
                        $query->where('clients.lastname', 'like', '%'. $param1.'%')
                            ->where('clients.firstname', 'like', '%'. $param2.'%');
                    });
                    $query->orWhere(function($query) use ($param1, $param2){
                        $query->where('clients.lastname', 'like', '%'. $param2.'%')
                            ->where('clients.firstname', 'like', '%'. $param1.'%');
                    });
                });
            }
            else
            { //иначе когда нет пробелов
                $query->orWhere('clients.lastname', 'like', '%'. $value.'%');

                $query->orWhere('clients.firstname', 'like', '%'. $value.'%');
            }

            $query->orWhere('clients.company_name', 'like', '%'. $value.'%');

            $query->orWhere('client_phones.phone', 'like', '%'. $value.'%');

            $query->orWhere('client_emails.email', 'like', '%'. $value.'%');

            $query->orWhere('client_inns.number', 'like', '%'. $value.'%');

            $query->orWhere('worksheets.id', $value);
        });
    }
}
