<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ContractFilter extends AbstractFilter
{
    public const IDS        = 'ids';
    public const INIT       = 'init';
    public const OVERDUE    = 'overdue';
    public const ISCLOSE      = 'isclose';
    public const DKP        = 'dkp';
    public const PDKP       = 'pdkp';
    public const CREATE     = 'create';
    public const CLOSE      = 'close';
    public const SALE       = 'sale';
    public const DKP_MANAGER = 'dkp_manager';
    public const PDKP_MANAGER = 'pdkp_manager';
    public const SALE_MANAGER = 'sale_manager';



    protected function getCallbacks(): array
    {
        return [
            self::INIT              => [$this, 'init'],
            self::IDS               => [$this, 'ids'],
            self::OVERDUE           => [$this, 'overdue'],
            self::ISCLOSE           => [$this, 'isClose'],
            self::DKP               => [$this, 'dkp'],
            self::PDKP              => [$this, 'pdkp'],
            self::CREATE            => [$this, 'create'],
            self::CLOSE             => [$this, 'close'],
            self::SALE              => [$this, 'sale'],
            self::DKP_MANAGER       => [$this, 'dkp_manager'],
            self::PDKP_MANAGER      => [$this, 'pdkp_manager'],
            self::SALE_MANAGER      => [$this, 'sale_manager'],
        ];
    }



    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder->leftJoin('wsm_reserve_new_cars',     'wsm_reserve_new_cars.id',    'wsm_reserve_new_car_contracts.reserve_id'); //Резерв
        $builder->leftJoin('cars',         'cars.id',        'wsm_reserve_new_cars.car_id'); //машина
        $builder->leftJoin('worksheets',   'worksheets.id',  'wsm_reserve_new_cars.worksheet_id'); //РЛ
        $builder->leftJoin('clients',      'clients.id',     'worksheets.client_id'); //Клиент
        $builder->leftJoin('wsm_reserve_sales', 'wsm_reserve_sales.reserve_id', 'wsm_reserve_new_cars.id');


    }



    /**
     * Выбранные ИД
     */
    public function ids(Builder $builder, array $array)
    {
        $builder->whereIn('wsm_reserve_new_car_contracts.id', $array);
    }



    public function overdue(Builder $builder, int $val)
    {
        $builder->where(function($q){
            $q->whereNotNull('wsm_reserve_new_car_contracts.pdkp_delivery_at');
            $q->whereDate('wsm_reserve_new_car_contracts.pdkp_delivery_at', '<', now());
            $q->whereNull('wsm_reserve_new_car_contracts.dkp_offer_at');
        });
    }



    public function isClose(Builder $builder, int $val)
    {
        $builder->whereNotNull('wsm_reserve_new_car_contracts.dkp_closed_at');
    }



    public function create(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($paidDate[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.created_at', [$date_1, $date_2]);
    }



    public function dkp(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($paidDate[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.dkp_offer_at', [$date_1, $date_2]);
    }



    public function pdkp(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($paidDate[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.pdkp_offer_at', [$date_1, $date_2]);
    }



    public function sale(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($paidDate[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_sales.date_at', [$date_1, $date_2]);
    }



    public function close(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($paidDate[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.dkp_closed_at', [$date_1, $date_2]);
    }



    public function dkp_manager(Builder $builder, int $val)
    {
        $builder->where('wsm_reserve_new_car_contracts.dkp_decorator_id', $val);
    }



    public function pdkp_manager(Builder $builder, int $val)
    {
        $builder->where('wsm_reserve_new_car_contracts.pdkp_decorator_id', $val);
    }



    public function sale_manager(Builder $builder, int $val)
    {
        $builder->where('wsm_reserve_sales.decorator_id', $val);
    }
}
