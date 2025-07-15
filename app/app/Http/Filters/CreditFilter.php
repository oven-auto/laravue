<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class CreditFilter extends AbstractFilter
{
    public const INIT   = 'init';
    public const IDS = 'ids';
    public const SORT = 'sort';
    public const SEARCH = 'search';
    public const WORKSHEET = 'worksheet_id';



    protected function getCallbacks(): array
    {
        return [
            self::INIT          => [$this, 'init'],
            self::IDS           => [$this, 'ids'],
            self::SORT          => [$this, 'sort'],
            self::SEARCH        => [$this, 'search'],
            self::WORKSHEET     => [$this, 'worksheet'],
        ];
    }



    public function __construct(array $queryParams)
    {
        $queryParams['sort'] = $queryParams['sort'] ?? '';

        $queryParams['init'] = 'init';

        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder
            ->leftJoin('worksheets', 'worksheets.id', 'wsm_credits.worksheet_id')
            ->leftJoin('clients', 'clients.id', 'worksheets.client_id')
            ->leftJoin('client_phones', 'client_phones.client_id', 'clients.id')
            ->leftJoin('wsm_credit_awards', 'wsm_credit_awards.wsm_credit_id', 'wsm_credits.id')
            ->leftJoin('wsm_credit_calculations', 'wsm_credit_calculations.wsm_credit_id', 'wsm_credits.id')
            ->leftJoin('wsm_credit_cars', 'wsm_credit_cars.wsm_credit_id', 'wsm_credits.id')
            ->leftJoin('wsm_credit_contracts', 'wsm_credit_contracts.wsm_credit_id', 'wsm_credits.id')
            ->leftJoin('wsm_credit_deductions', 'wsm_credit_deductions.wsm_credit_id', 'wsm_credits.id')
            ->leftJoin('wsm_credit_services', 'wsm_credit_services.wsm_credit_id', 'wsm_credits.id');           
    }



    public function worksheet(Builder $builder, int $value)
    {
        $builder->where('wsm_credits.worksheet_id', $value);
    }



    public function ids(Builder $builder, array $val)
    {
        $builder->whereIn('wsm_credits.id', $val);
    }



    public function sort(Builder $builder, string $val)
    {

    }



    public function search(Builder $builder, string $value)
    {
        $builder->where(function($subQ) use ($value){
            $subQ->where('clients.lastname', 'LIKE', '%'.$value.'%');
            $subQ->orWhere('clients.company_name', 'LIKE', '%'.$value.'%');
            $subQ->orWhere('client_phones.phone', 'LIKE', '%'.$value.'%');
        });
    }
}