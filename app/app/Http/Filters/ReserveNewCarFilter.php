<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ReserveNewCarFilter extends AbstractFilter
{
    public const IDS                        = 'ids';
    public const TRASH                      = 'trash';
    public const VIN                        = 'vin';
    public const INIT                       = 'init';
    public const BRAND                      = 'brand_id';
    public const MARK                       = 'mark_id';
    public const COMPLECTATION_CODE         = 'complectation_code';
    public const EXECUTORS                  = 'executors';

    protected function getCallbacks(): array
    {
        return [
            self::INIT                              => [$this, 'init'],
            self::IDS                               => [$this, 'ids'],
            self::TRASH                             => [$this, 'trash'],
            self::VIN                               => [$this, 'vin'],
            self::BRAND                             => [$this, 'brand'],
            self::MARK                              => [$this, 'mark'],
            self::COMPLECTATION_CODE                => [$this, 'complectationCode'],
            self::EXECUTORS                         => [$this, 'executors'],
        ];
    }



    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder
            ->leftJoin('cars', 'cars.id', 'wsm_reserve_new_cars.car_id') //машина
            ->leftJoin('complectations', 'complectations.id', 'cars.complectation_id') //комплектация машины
            ->leftJoin('worksheets', 'worksheets.id', 'wsm_reserve_new_cars.worksheet_id') //РЛ
            ->leftJoin('worksheet_executors', 'worksheet_executors.worksheet_id', 'worksheets.id') //Участники РЛ
            ->leftJoin('clients', 'clients.id', 'worksheets.client_id') //Клиент
            ->leftJoin('wsm_reserve_new_car_contracts', 'wsm_reserve_new_car_contracts.reserve_id', 'wsm_reserve_new_cars.id'); //Контракт
    }



    /**
     * Участники РЛ
     */
    public function executors(Builder $builder, array $array)
    {
        $builder->whereIn('worksheet_executors.user_id', $array);
    }



    /**
     * Выделенные ИД
     */
    public function ids(Builder $builder, array $array)
    {
        $builder->whereIn('wsm_reserve_new_cars.id', $array);
    }



    /**
     * Совпадение ВИНа
     */
    public function vin(Builder $builder, $value)
    {
        $builder->where('cars.vin', 'LIKE', '%' . $value . '%');
    }



    /**
     * Бренд автомобиля
     */
    public function brand(Builder $builder, $value)
    {
        $builder->where('cars.brand_id', $value);
    }



    /**
     * Модель автомобиля
     */
    public function mark(Builder $builder, $value)
    {
        $builder->where('cars.mark_id', $value);
    }



    /**
     * Код комплектации
     */
    public function complectationCode(Builder $builder, $value)
    {
        $builder->where('complectations.code', 'LIKE', '%' . $value . '%');
    }
}
