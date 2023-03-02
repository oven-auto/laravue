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
        ];
    }

    private function checkJoin(Builder $builder, $table)
    {
        $res = collect($builder->getQuery()->joins)->pluck('table')->contains($table);
        return $res;
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
        $builder->orWhere('client_phones.phone', 'like', '%'. $value.'%');

        // if(!$this->checkJoin($builder, 'client_emails'))
        //     $builder->leftJoin('client_emails', 'client_emails.client_id','clients.id');
        // $builder->orWhere('client_emails.email', 'like', '%'. $value.'%');

        $builder->orWhere('clients.lastname', 'like', '%'. $value.'%');

        $builder->orWhere('clients.id', $value);
    }

}
