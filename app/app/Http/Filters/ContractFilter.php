<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ContractFilter extends AbstractFilter
{
    public const IDS        = 'ids';
    public const INIT       = 'init';


    private const CONTRACT_TABLE = 'wsm_reserve_new_car_contracts';
    private const RESERVE_TABLE = 'wsm_reserve_new_cars';
    private const CAR_TABLE = 'cars';
    private const WORKSHEET_TABLE = 'worksheets';
    private const CLIENT_TABLE = 'clients';


    protected function getCallbacks(): array
    {
        return [
            self::INIT              => [$this, 'init'],
            self::IDS               => [$this, 'ids'],
        ];
    }



    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder->leftJoin(self::RESERVE_TABLE,     self::RESERVE_TABLE . '.id',    self::CONTRACT_TABLE . '.reserve_id'); //Резерв
        $builder->leftJoin(self::CAR_TABLE,         self::CAR_TABLE . '.id',        self::RESERVE_TABLE . '.car_id'); //машина
        $builder->leftJoin(self::WORKSHEET_TABLE,   self::WORKSHEET_TABLE . '.id',  self::RESERVE_TABLE . '.worksheet_id'); //РЛ
        $builder->leftJoin(self::CLIENT_TABLE,      self::CLIENT_TABLE . '.id',     self::WORKSHEET_TABLE . '.client_id'); //Клиент
    }



    /**
     * Выбранные ИД
     */
    public function ids(Builder $builder, array $array)
    {
        $builder->whereIn('wsm_reserve_new_car_contracts.id', $array);
    }
}
