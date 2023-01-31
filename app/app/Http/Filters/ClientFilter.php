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

    protected function getCallbacks(): array
    {
        return [
            self::LASTNAME          => [$this, 'lastname'],
            self::FIRSTNAME               => [$this, 'firstname'],
            self::FATHERNAME  => [$this, 'fathername'],
            self::PHONE           => [$this, 'phone'],
            self::EMAIL          => [$this, 'email'],
        ];
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
        $builder->where('clients.phone', 'like', '%'. $value.'%');
    }

    public function email(Builder $builder, $value)
    {
        $builder->where('clients.email', 'like', '%'. $value.'%');
    }

}
