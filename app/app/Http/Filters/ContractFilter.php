<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @OA\Schema(
 *   description = "Параметры фильтрации контрактов"
 * )
 */
class ContractFilter extends AbstractFilter
{
    public const IDS            = 'ids';

    public const INIT           = 'init';

    /**  @OA\Property(
     * format="bool", 
     * description="Просроченные, 1 - есть, 0 нет.", 
     * property="overdue", 
     * type="bool", 
     * example="1")
     * */
    public const OVERDUE        = 'overdue';

    /**  @OA\Property(
     * format="bool", 
     * description="Расторгнутые, 1 - есть, 0 нет.", 
     * property="isclose", 
     * type="bool", 
     * example="1")
     * */
    public const ISCLOSE        = 'isclose';
    
     /**  @OA\Property(
     *      format="array", 
     *      description="Массив содержащий интервал даты ДКП от - до, параметр ДО необязателен, 
     *      отсутствие второго параметра, будет означать, что используется не интервал, 
     *      соответственно сравнение будет строго по одному параметру", 
     *      property="dkp", 
     *      type="array", 
     *      example="[01.10.2024,22.10.2024]", 
     *      @OA\Items()
     * )
     * */
    public const DKP            = 'dkp';

     /**  @OA\Property(
     *      format="array", 
     *      description="Массив содержащий интервал даты ПДКП от - до, параметр ДО необязателен, 
     *      отсутствие второго параметра, будет означать, что используется не интервал, 
     *      соответственно сравнение будет строго по одному параметру", 
     *      property="pdkp", 
     *      type="array", 
     *      example="[01.10.2024,22.10.2024]", 
     *      @OA\Items()
     * )
     * */
    public const PDKP           = 'pdkp';

     /**  @OA\Property(
     *      format="array", 
     *      description="Массив содержащий интервал даты Первичный контракт от - до, параметр ДО необязателен, 
     *      отсутствие второго параметра, будет означать, что используется не интервал, 
     *      соответственно сравнение будет строго по одному параметру", 
     *      property="create", 
     *      type="array", 
     *      example="[01.10.2024,22.10.2024]", 
     *      @OA\Items()
     * )
     * */
    public const CREATE         = 'create';

     /**  @OA\Property(
     *      format="array", 
     *      description="Массив содержащий интервал даты Расторжение от - до, параметр ДО необязателен, 
     *      отсутствие второго параметра, будет означать, что используется не интервал, 
     *      соответственно сравнение будет строго по одному параметру", 
     *      property="close", 
     *      type="array", 
     *      example="[01.10.2024,22.10.2024]", 
     *      @OA\Items()
     * )
     * */
    public const CLOSE          = 'close';

     /**  @OA\Property(
     *      format="array", 
     *      description="Массив содержащий интервал даты Продажа от - до, параметр ДО необязателен, 
     *      отсутствие второго параметра, будет означать, что используется не интервал, 
     *      соответственно сравнение будет строго по одному параметру", 
     *      property="sale", 
     *      type="array", 
     *      example="[01.10.2024,22.10.2024]", 
     *      @OA\Items()
     * )
     * */
    public const SALE           = 'sale';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы Оформителей ДКП", 
     * property="dkp_manager", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const DKP_MANAGER    = 'dkp_manager';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы Оформителей ПДКП", 
     * property="pdkp_manager", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const PDKP_MANAGER   = 'pdkp_manager';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы Оформителей продажи", 
     * property="sale_manager", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const SALE_MANAGER   = 'sale_manager';

    /**  @OA\Property(
     * format="string", 
     * description="Полнотекстовый поиск, если перед строкой поиска подставить id@ будет искать по id машины. Возможны следующие уточнения:
     * id@ - id машины (точный поиск), vin@ - вин машины, order@ - номер заказа, client@ - фамилия клиента", 
     * property="search", 
     * type="string", 
     * example="1122")
     * */
    public const SEARCH = 'search';//Полнотекстовы поиск



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
            self::SEARCH            => [$this, 'search'],
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
        $builder->leftJoin('car_orders', 'car_orders.car_id', 'cars.id');

    }



    /**
     * Выбранные ИД
     */
    public function ids(Builder $builder, array $array)
    {
        $builder->whereIn('wsm_reserve_new_car_contracts.id', $array);
    }



    public function overdue(Builder $builder, bool $val)
    {
        if($val)
            $builder->where(function($q){
                $q->whereNotNull('wsm_reserve_new_car_contracts.pdkp_delivery_at');
                $q->whereDate('wsm_reserve_new_car_contracts.pdkp_delivery_at', '<', now());
                $q->whereNull('wsm_reserve_new_car_contracts.dkp_offer_at');
            });
    }



    public function isClose(Builder $builder, bool $val)
    {
        if($val)
            $builder->whereNotNull('wsm_reserve_new_car_contracts.dkp_closed_at');
    }



    public function create(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($dates[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.created_at', [$date_1, $date_2]);
    }



    public function dkp(Builder $builder, array $dates)
    {  
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($dates[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.dkp_offer_at', [$date_1, $date_2]);
    }



    public function pdkp(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($dates[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.pdkp_offer_at', [$date_1, $date_2]);
    }



    public function sale(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($dates[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_sales.date_at', [$date_1, $date_2]);
    }



    public function close(Builder $builder, array $dates)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $dates[0])->format('Y-m-d');
        $date_2 = isset($dates[1]) ? Carbon::createFromFormat('d.m.Y', $dates[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_car_contracts.dkp_closed_at', [$date_1, $date_2]);
    }



    public function dkp_manager(Builder $builder, array $val)
    {
        $builder->whereIn('wsm_reserve_new_car_contracts.dkp_decorator_id', $val);
    }



    public function pdkp_manager(Builder $builder, array $val)
    {
        $builder->whereIn('wsm_reserve_new_car_contracts.pdkp_decorator_id', $val);
    }



    public function sale_manager(Builder $builder, array $val)
    {
        $builder->whereIn('wsm_reserve_sales.decorator_id', $val);
    }



    public function search(Builder $builder, string $value)
    {
        $value = str_contains($value, '@') ? $value : '@'.$value;
        $value = explode('@', $value);
        list($column, $val) = $value;

        switch($column){
            case 'id':
                $builder->where('cars.id', $val);
                break;
            case 'vin':
                $builder->where('cars.vin', 'LIKE', '%' . $val . '%');
                break;
            case 'order':
                $builder->where('car_orders.order_number', 'LIKE', '%' . $val . '%');
                break;
            case 'client':
                $builder->where('clients.lastname', 'LIKE', '%' . $val . '%');
                break;
            default:
                $builder->where(function ($query) use ($val) {
                    $query->where('cars.vin',                   'LIKE', '%' . $val . '%')
                        ->orWhere('cars.id',                    'LIKE', '%' . $val . '%')
                        ->orWhere('car_orders.order_number',    'LIKE', '%' . $val . '%');
                });
                break;
        }
    }
}
