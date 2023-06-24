<?php

namespace App\Http\Filters;
use Illuminate\Database\Eloquent\Builder;

Class WorksheetFilter extends AbstractFilter
{
    public const INPUT = 'input';
    public const TASK_ID = 'task_id';
    public const CLIENT_TYPE = 'client_type';
    public const APPEAL_ID = 'appeal_id';
    public const SALON_IDS = 'salon_ids';
    public const STRUCTURE_ID = 'structure_id';
    public const EXECUTOR_IDS = 'executor_ids';
    public const STATUS_ID = 'status_id';
    public const AUTHOR_ID = 'author_id';
    public const IDS = 'ids';
    public const INTERVAL = 'interval';
    public const BEGIN_AT = 'begin_at';
    public const END_AT = 'end_at';
    public const ACTION_STATUS = 'action_status';

    protected function getCallbacks(): array
    {
        return [
            self::INPUT          => [$this, 'input'],
            self::TASK_ID       =>  [$this, 'taskId'],
            self::CLIENT_TYPE   => [$this, 'clientType'],
            self::APPEAL_ID   => [$this, 'appealId'],
            self::SALON_IDS   => [$this, 'salonIds'],
            self::STRUCTURE_ID   => [$this, 'structureId'],
            self::EXECUTOR_IDS => [$this, 'executorIds'],
            self::STATUS_ID => [$this, 'statusId'],
            self::AUTHOR_ID => [$this, 'authorId'],
            self::IDS   =>  [$this,'ids'],
            self::INTERVAL => [$this, 'interval'],
            self::BEGIN_AT => [$this, 'beginAt'],
            self::END_AT => [$this, 'endAt'],
            self::ACTION_STATUS => [$this, 'actionStatus'],
        ];
    }

    public function actionStatus(Builder $builder, $value)
    {
        $builder->where('worksheet_actions.status', $value);
    }

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

    public function statusId(Builder $builder, $value)
    {
        $builder->where('worksheets.status_id',$value);
    }

    public function executorIds(Builder $builder, $value)
    {
        $builder->leftJoin('worksheet_executors','worksheet_executors.worksheet_id','worksheets.id');

        if(!$this->checkJoin($builder, 'users'))
            $builder->leftJoin('users', function($join) {
                $join->on('users.id', 'worksheets.author_id');
                $join->orOn('users.id','worksheet_executors.user_id');
            });

        $builder->whereIn('users.id', $value);
    }

    public function salonIds(Builder $builder, $value)
    {
        $builder->whereIn('worksheets.company_id', $value);
    }

    public function structureId(Builder $builder, $value)
    {
        $builder->where('worksheets.structure_id', $value);
    }

    public function appealId(Builder $builder, $value)
    {
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

        // if(!$this->checkJoin($builder, 'users'))
        //     $builder->leftJoin('users','users.id','worksheets.author_id');

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

            // $query->orWhere('users.lastname', $value);
        });
    }
}
