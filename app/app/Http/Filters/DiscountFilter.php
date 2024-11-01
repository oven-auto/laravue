<?php

namespace App\Http\Filters;

use App\Models\WsmReserveNewCar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

Class DiscountFilter extends AbstractFilter
{
    public const IDS = 'ids';
    public const TYPE = 'type';
    public const INPUT = 'input';
    public const INIT = 'init';



    public function __construct($queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder->leftJoin('worksheets', 'worksheets.id', 'discounts.worksheet_id')
            ->leftJoin('clients', 'clients.id', 'worksheets.client_id')
            ->leftJoin('wsm_reserve_new_cars', function($join){
                $join->on('wsm_reserve_new_cars.id', 'discounts.modulable_id');
            })
            ->leftJoin('cars', 'cars.id', 'wsm_reserve_new_cars.car_id')
            ->where(function($query){
                $query->orWhere(function($queryReserve){
                    $queryReserve->where('discounts.modulable_id', DB::raw('wsm_reserve_new_cars.id'))
                        ->where('discounts.modulable_type', 'App\\Models\\WsmReserveNewCar');
                });
            })
            ->whereNull('wsm_reserve_new_cars.deleted_at');
    }



    public function getCallbacks() : array
    {
        return [
            self::INIT => [$this, 'init'],
            self::IDS => [$this, 'ids'],
            self::TYPE => [$this, 'type'],
            self::INPUT => [$this, 'input'],
        ];
    }



    public function ids(Builder $builder, array $array)
    {
        $builder->whereIn('discounts.id', $array);
    }



    public function type(Builder $builder, $value)
    {
        $builder->where('discounts.discount_type_id', $value);
    }



    public function input(Builder $builder, $value)
    {
        $builder->where(function($query) use ($value){
            $query->orWhere('clients.lastname', 'LIKE', '%'.$value.'%');
            $query->orWhere('cars.vin', 'LIKE', '%'.$value.'%');
        });
    }
}