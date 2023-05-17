<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ClientFilter extends AbstractFilter
{
    public const LASTNAME = 'lastname';
    public const FIRSTNAME = 'firstname';
    public const FATHERNAME = 'fathername';
    public const PHONE = 'phone';
    public const EMAIL = 'email';
    public const CLIENT_TYPE_ID = 'client_type_id';
    public const TRAFIC_SEX_ID = 'trafic_sex_id';
    public const TRAFIC_ZONE_ID = 'trafic_zone_id';
    public const HAS_WORKSHEET = 'has_worksheet';
    public const INPUT         = 'input';
    public const REGISTER_INTERVAL = 'register_interval';
    public const REGISTER_START         = 'register_start';
    public const REGISTER_END           = 'register_end';
    public const ACTION_INTERVAL        = 'action_interval';
    public const ACTION_START           = 'action_start';
    public const ACTION_END             = 'action_end';
    public const LOYALTY_ID             = 'loyalty_id';
    public const IDS                    = 'ids';

    protected function getCallbacks(): array
    {
        return [
            self::LASTNAME                  => [$this, 'lastname'],
            self::FIRSTNAME                 => [$this, 'firstname'],
            self::FATHERNAME                => [$this, 'fathername'],
            self::PHONE                     => [$this, 'phone'],
            self::EMAIL                     => [$this, 'email'],
            self::CLIENT_TYPE_ID            => [$this, 'clientTypeId'],
            self::TRAFIC_SEX_ID             => [$this, 'traficSexId'],
            self::TRAFIC_ZONE_ID            => [$this, 'traficZoneId'],
            self::HAS_WORKSHEET             => [$this, 'hasWorksheet'],
            self::INPUT                     => [$this, 'input'],
            self::REGISTER_INTERVAL         => [$this, 'registerInterval'],
            self::REGISTER_START            => [$this, 'registerStart'],
            self::REGISTER_END              => [$this, 'registerEnd'],
            self::ACTION_INTERVAL           => [$this, 'actionInterval'],
            self::ACTION_START              => [$this, 'actionStart'],
            self::ACTION_END                => [$this, 'actionEnd'],
            self::LOYALTY_ID                => [$this, 'loyaltyId'],
            self::IDS                       => [$this, 'ids'],
        ];
    }

    private function checkJoin(Builder $builder, $table)
    {
        $res = collect($builder->getQuery()->joins)->pluck('table')->contains($table);
        return $res;
    }

    public function ids(Builder $builder, $value)
    {
        $builder->whereIn('clients.id', explode(',',$value));
    }

    public function lastname(Builder $builder, $value)
    {
        $builder->where('clients.lastname', 'like', '%'. $value.'%');
    }

    public function firstname(Builder $builder, $value)
    {
        $builder->where('clients.firstname', 'like', '%'. $value.'%');
    }

    public function fathername(Builder $builder, $value)
    {
        $builder->where('clients.fathername', 'like', '%'. $value.'%');
    }

    public function phone(Builder $builder, $value)
    {
        if(!$this->checkJoin($builder, 'client_phones'))
            $builder->leftJoin('client_phones', 'client_phones.client_id','clients.id');
        $builder->where('client_phones.phone', 'like', '%'. $value.'%');
    }

    public function email(Builder $builder, $value)
    {
        if(!$this->checkJoin($builder, 'client_emails'))
            $builder->leftJoin('client_emails', 'client_emails.client_id','clients.id');
        $builder->where('client_emails.email', 'like', '%'. $value.'%');
    }

    public function clientTypeId(Builder $builder, $value)
    {
        $builder->where('clients.client_type_id', $value);
    }

    public function traficSexId(Builder $builder, $value)
    {
        $builder->where('clients.trafic_sex_id', $value);
    }

    public function traficZoneId(Builder $builder, $value)
    {
        $builder->where('clients.trafic_zone_id', $value);
    }

    public function hasWorksheet(Builder $builder, $value)
    {
        $var = $value ? 1 : 0;
        $builder->leftJoin('worksheets','worksheets.client_id','clients.id');
        if($var)
            $builder->whereNotNull('worksheets.id');
        else
            $builder->whereNull('worksheets.id');
    }

    public function input(Builder $builder, $value)
    {
        if(!$this->checkJoin($builder, 'client_phones'))
            $builder->leftJoin('client_phones', 'client_phones.client_id','clients.id');

            if(!$this->checkJoin($builder, 'client_inns'))
            $builder->leftJoin('client_inns', 'client_inns.client_id','clients.id');

        if(!$this->checkJoin($builder, 'client_emails'))
            $builder->leftJoin('client_emails', 'client_emails.client_id','clients.id');

        $builder->where(function($query) use ($value){

            if(preg_match('/\s/',$value)) { //есть пробелы
                $query->orWhere(function($query) use ($value){
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
            else { //иначе когда нет пробелов
                $query->orWhere('clients.lastname', 'like', '%'. $value.'%');

                $query->orWhere('clients.firstname', 'like', '%'. $value.'%');
            }

            $query->orWhere('client_phones.phone', 'like', '%'. $value.'%');

            $query->orWhere('client_emails.email', 'like', '%'. $value.'%');

            $query->orWhere('client_inns.number', 'like', '%'. $value.'%');



            $query->orWhere('clients.id', $value);

            $query->orWhere('clients.company_name', 'like', '%'. $value.'%');
        });
    }

    public function registerInterval(Builder $builder, $value)
    {
        switch ($value) {
            case 'month':
                $builder->where(function($query)  {
                    $query
                        ->whereYear('clients.created_at', '=', now()->year)
                        ->whereMonth('clients.created_at', '=', now()->month);
                });
                break;
            case 'week':
                $builder->whereBetween('clients.created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;
            case 'today':
                $builder->whereDate('clients.created_at', now());
                break;
            case 'yesterday':
                $builder->whereDate('clients.created_at', now()->subDay());
                break;
            default:
                break;
        }
    }

    public function registerStart(Builder $builder, $value)
    {
        $builder->whereDate('clients.created_at','>=', $this->formatDate($value));
    }

    public function registerEnd(Builder $builder, $value)
    {
        $builder->whereDate('clients.created_at','<=', $this->formatDate($value));
    }

    public function actionInterval(Builder $builder, $value)
    {
        $builder->leftJoin('worksheets','worksheets.client_id','clients.id');
        switch ($value) {
            case 'month':
                $builder->where(function($query)  {
                    $query
                        ->whereYear('worksheets.created_at', '=', now()->year)
                        ->whereMonth('worksheets.created_at', '=', now()->month);
                });
                break;
            case 'week':
                $builder->whereBetween('worksheets.created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;
            case 'today':
                $builder->whereDate('worksheets.created_at', now());
                break;
            case 'yesterday':
                $builder->whereDate('worksheets.created_at', now()->subDay());
                break;
            default:
                break;
        }
    }

    public function actionStart(Builder $builder, $value)
    {
        $builder->leftJoin('worksheets','worksheets.client_id','clients.id');
        $builder->whereDate('worksheets.created_at','>=', $this->formatDate($value));
    }

    public function actionEnd(Builder $builder, $value)
    {
        $builder->leftJoin('worksheets','worksheets.client_id','clients.id');
        $builder->whereDate('worksheets.created_at','<=', $this->formatDate($value));
    }

    public function loyaltyId(Builder $builder, $value)
    {

    }

}
