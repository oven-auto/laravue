<?php

namespace App\Http\Filters;

use App\Models\CarState;
use App\Models\CarStatusType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *   description = "Параметры фильтрации резерва"
 * )
 */
class ReserveNewCarFilter extends AbstractFilter
{
    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы автомобилей", 
     * property="ids", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const IDS                        = 'ids';
    public const TRASH                      = 'trash';
    public const VIN                        = 'vin';
    public const INIT                       = 'init';
    public const COMPLECTATION_CODE         = 'complectation_code';
    public const EXECUTORS                  = 'executors';

    /**  @OA\Property(
     *      format="array", 
     *      description="Массив содержащий идентификаторы бренда", 
     *      property="brands", 
     *      type="array", 
     *      example="[1,2]", 
     *      @OA\Items(
     *      )
     * )
     * */
    public const BRAND                      = 'brands';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы моделей", 
     * property="models", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const MARK                       = 'models';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий интервал цены от - до, параметр ДО необязателен, 
     * отсутствие второго параметра, будет означать, что используется не интервал, 
     * соответственно сравнение будет строго по одному параметру", 
     * property="prices", 
     * type="array", 
     * example="[700000,1000000]", 
     * @OA\Items())
     * */
    public const PRICES                     = 'prices';

    /**  @OA\Property(
     * format="integer", 
     * description="Год выпуска", 
     * property="year", 
     * type="integer", 
     * example="2024")
     * */
    public const YEAR                       = 'year';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы типов кузова", 
     * property="bodyworks", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const BODY                       = 'bodyworks';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы типов двигателя", 
     * property="motortypes", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const MOTOR_TYPE                 = 'motortypes';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы типов трансмиссий", 
     * property="transmissions", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const TRANSMISSION               = 'transmissions';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы типов привода", 
     * property="drivers", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const DRIVER                     = 'drivers';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы цвета (БАЗОВЫЕ ЦВЕТА - НЕ ДИЛЕРСКИЕ)", 
     * property="colors", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const COLOR                      = 'colors';

     /**  @OA\Property(
     * format="bool", 
     * description="Наличие опций", 
     * property="has_options", 
     * type="bool"
     * )
     * */
    public const HAS_OPTIONS = 'has_options';

    /**  @OA\Property(
     *      format="bool", 
     *      description="Наличие переоценки, 1 - есть, 0 нет.", 
     *      property="has_overprice", 
     *      type="bool", 
     *      example="1"
     * )
     * */
    public const HAS_OVERPRICE = 'has_overprice';

    /**  @OA\Property(
     *      format="bool", 
     *      description="Наличие тюнинга, 1 - есть, 0 нет.", 
     *      property="has_tuning", 
     *      type="bool", 
     *      example="1")
     * */
    public const HAS_TUNING = 'has_tuning';

     /**  @OA\Property(
     * format="bool", 
     * description="Наличие установленого тюнинга(номенклатурно)", 
     * property="has_devices", 
     * type="bool"
     * )
     * */
    public const HAS_DEVICES = 'has_devices';

    /**  @OA\Property(
     * format="bool", 
     * description="Наличие подарка, 1 - есть, 0 нет.", 
     * property="has_gift", 
     * type="bool", 
     * example="1")
     * */
    public const HAS_GIFT = 'has_gift';

    /**  @OA\Property(
     * format="bool", 
     * description="Наличие скидки, 1 - есть, 0 нет.", 
     * property="has_discount", 
     * type="bool", 
     * example="1")
     * */
    public const HAS_DISCOUNT = 'has_discount';

     /**  @OA\Property(
     * format="bool", 
     * description="Наличие выкупного платежа", 
     * property="has_ransom", 
     * type="bool"
     * )
     * */
    public const HAS_RANSOM = 'has_ransom';

    /**  @OA\Property(
     * format="bool", 
     * description="Наличие дополнительной себестоимости, 1 - есть, 0 нет.", 
     * property="has_detailing_cost", 
     * type="bool", 
     * example="1")
     * */
    public const HAS_DETAILING_COST = 'has_detailing_cost';   




    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы текущих этапов поставки", 
     * property="logistic_statuses", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const STATE_STATUS = 'logistic_statuses';//Этап поставки 

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы типов товарных признаков ", 
     * property="trade_markers", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const TRADE_MARKER_ID = 'trade_markers';//Товарный признак

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы контрмарки", 
     * property="markers", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const MARKER_ID = 'markers';//Контрмарка

     /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий интервал даты этапа создание резерва от - до, параметр ДО необязателен, 
     * отсутствие второго параметра, будет означать, что используется не интервал, 
     * соответственно сравнение будет строго по одному параметру", 
     * property="reserve_date", 
     * type="array", 
     * example="[01.10.2024,22.10.2024]", 
     * @OA\Items())
     * */
    public const RESERVE_DATE = 'reserve_date';//интервал резерва

    /**
     * @OA\Property(
     *  format="bool", 
     *  description="Наличие контракта", 
     *  property="has_contract", 
     *  type="bool"
     * )
     */
    public const HAS_CONTRACT = 'has_contract';//есть контракт

     /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий интервал даты этапа первичноый контракт от - до, параметр ДО необязателен, 
     * отсутствие второго параметра, будет означать, что используется не интервал, 
     * соответственно сравнение будет строго по одному параметру", 
     * property="contract_date", 
     * type="array", 
     * example="[01.10.2024,22.10.2024]", 
     * @OA\Items())
     * */
    public const CONTRACT_DATE = 'contract_date';//Первичный контракт

     /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий интервал даты этапа ДКП от - до, параметр ДО необязателен, 
     * отсутствие второго параметра, будет означать, что используется не интервал, 
     * соответственно сравнение будет строго по одному параметру", 
     * property="dkp_date", 
     * type="array", 
     * example="[01.10.2024,22.10.2024]", 
     * @OA\Items())
     * */
    public const DKP_DATE = 'dkp_date';//dkp date

     /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий интервал даты этапа ПДКП от - до, параметр ДО необязателен, 
     * отсутствие второго параметра, будет означать, что используется не интервал, 
     * соответственно сравнение будет строго по одному параметру", 
     * property="pdkp_date", 
     * type="array", 
     * example="[01.10.2024,22.10.2024]", 
     * @OA\Items())
     * */
    public const PDKP_DATE = 'pdkp_date';//pdkp date

    /**
     * @OA\Property(
     *  format="bool", 
     *  description="Наличие оплаты", 
     *  property="has_client_pay", 
     *  type="bool"
     * )
     */
    public const HAS_CLIENT_PAY = 'has_client_pay';

    /**
     * @OA\Property(
     *  format="bool", 
     *  description="Наличие выдачи", 
     *  property="has_issue", 
     *  type="bool"
     * )
     */
    public const HAS_ISSUE = 'has_issue';

    /**
     * @OA\Property(
     *  format="bool", 
     *  description="Наличие продажи", 
     *  property="has_sale", 
     *  type="bool"
     * )
     */
    public const HAS_SALE = 'has_sale';

     /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий интервал даты этапа продажи от - до, параметр ДО необязателен, 
     * отсутствие второго параметра, будет означать, что используется не интервал, 
     * соответственно сравнение будет строго по одному параметру", 
     * property="sale_date", 
     * type="array", 
     * example="[01.10.2024,22.10.2024]", 
     * @OA\Items())
     * */
    public const SALE_DATE = 'sale_date';

    /**  @OA\Property(
     * format="bool", 
     * description="Наличие списания, 1 - есть, 0 нет.", 
     * property="has_off", 
     * type="bool", 
     * example="1")
     * */
    public const HAS_OFF = 'has_off';

     /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий тип рапотра", 
     * property="report_type", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const REPORT_TYPE = 'report_type';

     /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий интервал даты этапа списание от - до, параметр ДО необязателен, 
     * отсутствие второго параметра, будет означать, что используется не интервал, 
     * соответственно сравнение будет строго по одному параметру", 
     * property="off_date", 
     * type="array", 
     * example="[01.10.2024,22.10.2024]", 
     * @OA\Items())
     * */
    public const OFF_DATE = 'off_date';//Все логистические даты(используется мидлвар)

    /**  @OA\Property(
     * format="bool", 
     * description="Наличие в плане поставщика, 1 - есть, 0 нет.", 
     * property="has_plan", 
     * type="bool", 
     * example="1")
     * */
    public const HAS_PLAN = 'has_plan';

    /**  @OA\Property(
     * format="string", 
     * description="Полнотекстовый поиск, если перед строкой поиска подставить id@ будет искать по id машины. Возможны следующие уточнения:
     * id@ - id машины (точный поиск), vin@ - вин машины, order@ - номер заказа, model@ - модель машины", 
     * property="search", 
     * type="string", 
     * example="1122")
     * */
    public const SEARCH = 'search';//Полнотекстовы поиск

    public const LOGISTIC_DATES = 'logistic_dates';

    public const SORT = 'sort';

    public const POWER = 'power';

    public const TYPE_STATUS = 'type_statuses';

    public const HAS_TRADEIN = 'has_tradein';

    protected function getCallbacks(): array
    {
        return [
            self::SEARCH                            => [$this, 'search'],
            self::INIT                              => [$this, 'init'],
            self::IDS                               => [$this, 'ids'],
            self::TRASH                             => [$this, 'trash'],
            self::VIN                               => [$this, 'vin'],

            self::BRAND                             => [$this, 'brand'],
            self::MARK                              => [$this, 'mark'],
            self::COMPLECTATION_CODE                => [$this, 'complectationCode'],
            self::EXECUTORS                         => [$this, 'executors'],
            self::PRICES                            => [$this, 'price'],
            self::YEAR                              => [$this, 'year'],
            self::MOTOR_TYPE                        => [$this, 'motorTypes'],
            self::BODY                              => [$this, 'bodyWork'],
            self::TRANSMISSION                      => [$this, 'transmission'],
            self::DRIVER                            => [$this, 'drivers'],
            self::COLOR                             => [$this, 'colors'],

            self::HAS_OPTIONS                       => [$this, 'hasOptions'],
            self::HAS_OVERPRICE                     => [$this, 'hasOverprice'],
            self::HAS_TUNING                        => [$this, 'hasTuning'],
            self::HAS_DEVICES                       => [$this, 'hasDevices'],
            self::HAS_GIFT                          => [$this, 'hasGift'],
            self::HAS_DISCOUNT                      => [$this, 'hasDiscount'],
            self::HAS_RANSOM                        => [$this, 'hasRansom'],
            self::HAS_DETAILING_COST                => [$this, 'hasDetailingCost'],
            
            self::STATE_STATUS                      => [$this, 'stateStatus'],
            self::TRADE_MARKER_ID                   => [$this, 'tradeMarkerId'],
            self::MARKER_ID                         => [$this, 'markerId'],
            self::RESERVE_DATE                      => [$this, 'reserveDate'],
            self::HAS_CONTRACT                      => [$this, 'hasContract'],
            self::CONTRACT_DATE                     => [$this, 'contractDate'],
            self::DKP_DATE                          => [$this, 'dkpDate'],
            self::PDKP_DATE                         => [$this, 'pdkpDate'],

            self::HAS_CLIENT_PAY                    => [$this, 'hasClientPay'],
            self::HAS_ISSUE                         => [$this, 'hasIssue'],
            self::HAS_SALE                          => [$this, 'hasSale'],
            self::SALE_DATE                         => [$this, 'saleDate'],
            self::HAS_OFF                           => [$this, 'hasOff'],
            self::REPORT_TYPE                       => [$this, 'reportType'],
            self::HAS_PLAN                          => [$this, 'hasPlan'],
            self::LOGISTIC_DATES                    => [$this, 'logisticDates'],
            self::POWER                             => [$this, 'power'],
            self::HAS_TRADEIN                        => [$this, 'has_tradein'],

            self::SORT                              => [$this, 'sort'],
            self::TYPE_STATUS                       => [$this, 'typeStatuses'],
        ];
    }



    public function __construct(array $queryParams)
    {   
        $queryParams['init'] = $queryParams;
        parent::__construct($queryParams);
    }



    public function init(Builder $builder, array $params)
    {
        $builder
            ->leftJoin('cars', 'cars.id', 'wsm_reserve_new_cars.car_id') //машина
            ->leftJoin('complectations', 'complectations.id', 'cars.complectation_id') //комплектация машины
            ->leftJoin('motors', 'motors.id', 'complectations.motor_id')
            ->leftJoin('worksheets', 'worksheets.id', 'wsm_reserve_new_cars.worksheet_id') //РЛ
            ->leftJoin('worksheet_executors', 'worksheet_executors.worksheet_id', 'worksheets.id') //Участники РЛ
            ->leftJoin('clients', 'clients.id', 'worksheets.client_id') //Клиент
            ->leftJoin('wsm_reserve_new_car_contracts as contract', 'contract.reserve_id', 'wsm_reserve_new_cars.id'); //Контракт
        
        $this->setJoinToSearch($builder, $params);

        if(isset($queryParams['having']) && $queryParams['having'] > 0)
            $builder->havingRaw('count(cars.id) > '.($queryParams['having']-1));
    }



    public function setJoinToSearch(Builder $builder, array $params)
    {
        //if(isset($params['prices']))
            $builder ->leftJoin('car_full_prices as cfp', 'cfp.car_id', 'cars.id')//представление хранящее актуальную цену авто по прайсу
                ->leftJoin('wsm_reserve_complectation_prices as wrcp','wrcp.contract_id', 'contract.id')//сохраненая в контракте цена
                ->leftJoin('complectation_prices as cp', 'cp.id', 'wrcp.complectation_price_id')//цены комплектации
                ->leftJoin('wsm_reserve_option_prices as wrop', 'wrop.contract_id', 'contract.id')//сохраненные в контракте опции
                ->leftJoin(DB::raw('(SELECT sum(option_prices.price) as sum_option, wsm_reserve_new_cars.car_id from option_prices 
                    left join wsm_reserve_option_prices on wsm_reserve_option_prices.option_price_id = option_prices.id 
                    left join wsm_reserve_new_car_contracts on wsm_reserve_new_car_contracts.id = wsm_reserve_option_prices.contract_id 
                    left join wsm_reserve_new_cars on wsm_reserve_new_cars.id = wsm_reserve_new_car_contracts.reserve_id 
                    where wsm_reserve_new_cars.car_id is not null and wsm_reserve_new_cars.deleted_at is not null
                    GROUP  BY  wsm_reserve_new_cars.car_id) as joinOptionPrice'), 'joinOptionPrice.car_id', 'cars.id'
                )
                ->addSelect([
                    'joinOptionPrice.sum_option as _sum_option',
                    'cp.price                   as _cp_price', 
                    'cp.id                      as _cp_id', 
                ]);
        
        //if(isset($params['prices']) || isset($params['has_gift']))
            $builder->leftJoin('car_gift_prices as gift', 'gift.car_id', 'cars.id')//gift
                ->addSelect([
                    'gift.price as _gift_price', 
                ]);
        
        //if(isset($params['prices']) || isset($params['has_overprice']))
            $builder->leftJoin('car_over_prices as overprice', 'overprice.car_id', 'cars.id')//overprice
                ->addSelect([
                    'overprice.price as _over_price',
                ]);

        //if(isset($params['prices']) || isset($params['has_tuning']))
            $builder->leftJoin('car_tuning_prices as tuning', 'tuning.car_id', 'cars.id')//цена тюнинга
                ->addSelect([
                    'tuning.price as _tuning_price',
                ]);

        //if(isset($params['has_options']))//has_option
            $builder->leftJoin('car_options', 'car_options.car_id', 'cars.id');

        //if(isset($params['has_devices']))//has_devices
            $builder->leftJoin('car_tunings', 'car_tunings.car_id', 'cars.id');

        //if(isset($params['has_discount']))
            $builder->leftJoin('discounts', function($join){
                $join->on('discounts.worksheet_id', '=', 'worksheets.id')
                    ->on('discounts.modulable_type', '=', DB::raw('"App\\\Models\\\WsmReserveNewCar"'))
                    ->on('discounts.modulable_id', 'wsm_reserve_new_cars.id');
            });

        //if(isset($params['has_ransom']))//has_ransom
            $builder->leftJoin('ransom_cars', 'ransom_cars.car_id', 'cars.id'); 

        //if(isset($params['has_detailing_cost']))//has_detailing_cost
            $builder->leftJoin('car_detailing_costs', 'car_detailing_costs.car_id', 'cars.id');

        //if(isset($params['trade_markers']))//trade_marker
            $builder->leftJoin('car_trade_markers', 'car_trade_markers.car_id', 'cars.id');

        //if(isset($params['markers']))//markers
            $builder->leftJoin('car_markers', 'car_markers.car_id', 'cars.id');

        //if(isset($params['has_client_pay']))
            $builder->leftJoin('wsm_reserve_payments', 'wsm_reserve_payments.reserve_id', 'wsm_reserve_new_cars.id');

        //if(isset($params['has_issue']))
            $builder->leftJoin('wsm_reserve_issues', 'wsm_reserve_issues.reserve_id', 'wsm_reserve_new_cars.id');

        //if(isset($params['has_sale']) || (isset($params['sale_date'])))
            $builder->leftJoin('wsm_reserve_sales', 'wsm_reserve_sales.reserve_id', 'wsm_reserve_new_cars.id');

        //if(
        //    isset($params['has_off']) || //has_off
        //    isset($params['report_type']) //report_type
        //)
            $builder->leftJoin('car_owners', 'car_owners.car_id', 'cars.id');
        
        //if(isset($params['logistic_dates']) && count($params['logistic_dates']))//логистика
            $builder->leftJoin('car_date_logistics', 'car_date_logistics.car_id', 'cars.id');

        //if(isset($params['colors']))
            $builder->leftJoin('dealer_colors', 'dealer_colors.id', 'cars.color_id');

        $builder->leftJoin('marks', 'marks.id', 'cars.mark_id');

        $builder->leftJoin('brands', 'brands.id', 'cars.brand_id');

        $builder->leftJoin('car_status_types', 'car_status_types.car_id', 'cars.id');

        $builder->leftJoin('wsm_reserve_trade_ins', 'wsm_reserve_trade_ins.reserve_id', 'wsm_reserve_new_cars.id');
        
        $builder->leftJoin('wsm_reserve_lisings', 'wsm_reserve_lisings.reserve_id', 'wsm_reserve_new_cars.id');
    
        $builder->groupBy('wsm_reserve_new_cars.id');
    }



    public function has_tradein(Builder $builder, $val)
    {
        if($val)
            $builder->whereNotNull('wsm_reserve_trade_ins.reserve_id');
        else
            $builder->whereNull('wsm_reserve_trade_ins.reserve_id');
    }   



    public function power(Builder $builder, array $power)
    {
        if(count($power) == 1)
            $power[] = $power[0];
        $builder->whereBetween('motors.power', $power);
    }



    public function typeStatuses(Builder $builder, array $value)
    {
        $arr = array_unique($value);

        $result = array_intersect($arr, CarStatusType::VALUES);

        $builder->whereIn('car_status_types.status', $result);
    }



    public function sort(Builder $builder, string $val)
    {
        match($val){
            // 'price_low'     => $builder
            //     ->orderBy('cfp.complectationprice', 'ASC'),
            // 'price_high'    => $builder
            //     ->orderBy('cfp.complectationprice', 'DESC'),
            'sale_old'      => $builder
                ->orderBy(DB::raw('IF(wsm_reserve_sales.id IS NOT NULL, 0, 1)'))
                ->orderBy('wsm_reserve_sales.date_at', 'ASC'),
            'sale_new'      => $builder
                ->orderBy(DB::raw('IF(wsm_reserve_sales.id IS NOT NULL, 0, 1)'))
                ->orderBy('wsm_reserve_sales.date_at', 'DESC'),
            // 'stock_old'     => $builder
            //     ->orderBy(DB::raw('IF(stocking_date IS NOT NULL, 0, 1)'))
            //     ->orderBy('stocking_date', 'ASC'),
            // 'stock_new'     => $builder
            //     ->orderBy(DB::raw('IF(stocking_date IS NOT NULL, 0, 1)'))
            //     ->orderBy('stocking_date', 'DESC'),
            
            'name_asc' => $builder
                ->orderBy('brands.name', 'ASC')
                ->orderBy('marks.name', 'ASC'),
            'name_desc' => $builder
                ->orderBy('brands.name', 'DESC')
                ->orderBy('marks.name', 'DESC'),
            default => '',
        };
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
     * Фильтр цены
     */
    public function price(Builder $builder, array $arrPrices)
    {
        $price_1 = $arrPrices[0];
        $price_2 = $arrPrices[1] ??  $arrPrices[0];
        
        $str = 'IF(joinOptionPrice.sum_option IS NOT NULL, joinOptionPrice.sum_option, 0) + 
            if(cp.price IS NOT NULL, cp.price, 0) + 
            if(overprice.price is not null, overprice.price, 0) +
            if(tuning.price IS NOT NULL, tuning.price, 0) - 
            if(gift.price is NOT NULL, gift.price, 0)';

        $havingWithContract = '(('.$str.') >= '.$price_1.' and ('.$str.') <= '.$price_2.')';

        $havingWithOutContract = '((MAX(cfp.price) >= '.$price_1.' and max(cfp.price) <= '.$price_2.'))';

        $builder->havingRaw('IF(cp.id IS NOT NULL, '.$havingWithContract.', '.$havingWithOutContract.')');
    }



    /**
     * Бренд автомобиля
     */
    public function brand(Builder $builder, array $value)
    {
        $builder->whereIn('cars.brand_id', $value);
    }



    /**
     * Модель автомобиля
     */
    public function mark(Builder $builder, array $value)
    {
        $builder->whereIn('cars.mark_id', $value);
    }



    /**
     * CAR YEAR
     */
    public function year(Builder $builder, int $value)
    {
        $builder->where('cars.year', $value);
    }



    /**
     * BODYWORK
     */
    public function bodyWork(Builder $builder, array $value)
    {
        $builder->whereIn('complectations.body_work_id', $value);
    }



     /**
     * Трансмиссия
     */
    public function transmission(Builder $builder, array $value)
    {
        $builder->whereIn('motors.motor_transmission_id', $value);
    }



    /**
     * Привод
     */
    public function drivers(Builder $builder, array $value)
    {
        $builder->whereIn('motors.motor_driver_id', $value);
    }



    /**
     * MOTOR TYPES
     */
    public function motorTypes(Builder $builder, array $types)
    {
        $builder->whereIn('motors.motor_type_id', $types);
    }



    /**
     * HAS OPTION
     */
    public function hasOptions(Builder $builder, bool $options)
    {
        if($options)
            $builder->whereNotNull('car_options.car_id');
        else
            $builder->whereNull('car_options.car_id');
    }



    /**
     * НАЛИЧИЕ ПЕРЕОЦЕНКИ
     */
    public function hasOverPrice(Builder $builder, bool $value)
    {
        if($value)
            $builder->where('overprice.price', '>', 0);
        else
            $builder->where(function($query) {
                $query->whereNull('overprice.price')
                    ->orWhere('overprice.price', 0);
            });
    }



    /**
     * НАЛИЧИЕ ТЮНИНГА
     */
    public function hasTuning(Builder $builder, bool $value)
    {
        if($value)
            $builder->whereNotNull('tuning.price');
        else
            $builder->whereNull('tuning.price');
    }



    /**
     * HAS DEVICES
     */
    public function hasDevices(Builder $builder, bool $device)
    {
        if($device)
            $builder->whereNotNull('car_tunings.car_id');
        else
            $builder->whereNull('car_tunings.car_id');
    }



    /**
     * НАЛИЧИЕ ПОДАРКА
     */
    public function hasGift(Builder $builder, bool $value)
    {
        if($value)
            $builder->whereNotNull('gift.price');
        else
            $builder->whereNull('gift.price');
    }



    /**
     * НАЛИЧИЕ СКИДКИ
     */
    public function hasDiscount(Builder $builder, bool $value)
    {
        if($value)
            $builder->whereNotNull('discounts.id');
        else
            $builder->whereNull('discounts.id');
    }



    /**
     * НАЛИЧИЕ ВЫКУПНОГО ПЛАТЕЖА
     */
    public function hasRansom(Builder $builder, bool $purchase)
    {
        if($purchase)
            $builder->whereNotNull('ransom_cars.car_id');
        else
            $builder->whereNull('ransom_cars.car_id');
    }



    /**
     * НАЛИЧИЕ ДОП СЕБЕСТОИМОСТИ
     */
    public function hasDetailingCost(Builder $builder, bool $value)
    {
        if($value)
            $builder->whereNotNull('car_detailing_costs.id');
        else
            $builder->whereNull('car_detailing_costs.id');
    }



    /**
     * Логистический статус (В заказе, в отгрузке и тд)
     */
    public function stateStatus(Builder $builder, array $value)
    {
        $baseState = CarState::pluck('status')->toArray();

        $arr = array_intersect($baseState, $value);

        $builder->whereIn('cars.status', $arr);
    }



    /**
     * Товарный признак
     */
    public function tradeMarkerId(Builder $builder, string|array $value)
    {
        if(is_string($value))
            $value = [$value];
        $builder->whereIn('car_trade_markers.trade_marker_id', $value);
    }



    /**
     * КонтрМарка
     */
    public function markerId(Builder $builder, string|array $value)
    {
        if(is_string($value))
            $value = [$value];
        $builder->whereIn('car_markers.marker_id', $value);
    }



    /**
     * ИНТЕРВАЛ ПОД ДАТУ СОЗДАНИЯ РЕЗЕРВА
     */
    public function reserveDate(Builder $builder, array $date)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $date[0])->format('Y-m-d');
        $date_2 = isset($date[1]) ? Carbon::createFromFormat('d.m.Y', $date[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_new_cars.created_at', [$date_1, $date_2]);
    }



    /**
     * НАЛИЧИЕ КОНТРАКТА
     */
    public function hasContract(Builder $builder, bool $val)
    {
        if($val)
            $builder->whereNotNull('contract.id');
        else
            $builder->whereNull('contract.id');
    }



    /**
     * ПЕРВИЧНЫЙ КОНТРАКТ
     */
    public function contractDate(Builder $builder, array $date)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $date[0])->format('Y-m-d');
        $date_2 = isset($date[1]) ? Carbon::createFromFormat('d.m.Y', $date[1])->format('Y-m-d') : $date_1;
        $builder->whereRaw(
            'IF(contract.pdkp_offer_at IS NOT NULL,'. 
            'contract.pdkp_offer_at BETWEEN "'.$date_1.'" and "'.$date_2.'",'.
            'contract.dkp_offer_at BETWEEN "'.$date_1.'" and "'.$date_2.'")'
        );
    }



    /**
     * DKP
     */
    public function dkpDate(Builder $builder, array $date)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $date[0])->format('Y-m-d');
        $date_2 = isset($date[1]) ? Carbon::createFromFormat('d.m.Y', $date[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('contract.dkp_offer_at', [$date_1, $date_2]);
    }



    /**
     * PDKP
     */
    public function pdkpDate(Builder $builder, array $date)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $date[0])->format('Y-m-d');
        $date_2 = isset($date[1]) ? Carbon::createFromFormat('d.m.Y', $date[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('contract.pdkp_offer_at', [$date_1, $date_2]);
    }



    /**
     * ОПЛАТА КЛИЕНТА
     */
    public function hasClientPay(Builder $builder, bool $val)
    {
        if($val)
            $builder->whereNotNull('wsm_reserve_payments.id');
        else
            $builder->whereNull('wsm_reserve_payments.id');
    }



    /**
     * Выдача
     */
    public function hasIssue(Builder $builder, bool $val)
    {
        if($val)
            $builder->whereNotNull('wsm_reserve_issues.id');
        else
            $builder->whereNull('wsm_reserve_issues.id');
    }



    /**
     * Продажа
     */
    public function hasSale(Builder $builder, bool $val)
    {
        if($val)
            $builder->whereNotNull('wsm_reserve_sales.id');
        else
            $builder->whereNull('wsm_reserve_sales.id');
    }



    /**
     * Дата продажи
     */
    public function saleDate(Builder $builder, array $date)
    {
        $date_1 = Carbon::createFromFormat('d.m.Y', $date[0])->format('Y-m-d');
        $date_2 = isset($date[1]) ? Carbon::createFromFormat('d.m.Y', $date[1])->format('Y-m-d') : $date_1;
        $builder->whereBetween('wsm_reserve_sales.date_at', [$date_1, $date_2]);
    }



    /**
     * НАЛИЧИЕ СПИСАНИЯ
     */
    public function hasOff(Builder $builder, bool $value)
    {
        if($value)
            $builder->whereNotNull('car_owners.id');
        else
            $builder->whereNull('car_owners.id');
    }



    /**
     * ТИП РАПОРТА
     */
    public function reportType(Builder $builder, array $reportTypes)
    {
        $builder->where(function($query) use($reportTypes) {
                $query->where(function($reportQuery) use($reportTypes){
                    foreach($reportTypes as $index => $type)
                        switch($type){
                            case '1':
                                $reportQuery->where(function($green) {
                                    $green->where('worksheets.client_id', DB::raw('car_owners.client_id'))
                                        ->whereNotNull('car_owners.client_id');
                                });
                                break;
                            case '2':
                                $reportQuery->where(function($yellow) {
                                    $yellow->where('worksheets.client_id', '<>', DB::raw('car_owners.client_id'))
                                        ->orWhereNull('worksheets.client_id')
                                        ->whereNotNull('car_owners.client_id');
                                });
                                break;
                            default:
                                break;
                        };
                });
        });
    }



    /**
     * ВСЕ ЛОГИСТИЧЕСКИЕ ДАТЫ
     */
    public function logisticDates(Builder $builder, array $data)
    {
        if(!count($data))
            return;
        
        $builder->where(function($dateQuery) use($data){
            foreach($data as $key => $dateInterval)
                $dateQuery->orWhere(function($builderOrderDate) use ($key, $dateInterval){
                    $date_1 = Carbon::createFromFormat('d.m.Y', $dateInterval[0])->format('Y-m-d');
                    $date_2 = isset($dateInterval[1]) ? Carbon::createFromFormat('d.m.Y', $dateInterval[1])->format('Y-m-d') : $date_1;
                    $builderOrderDate->where('car_date_logistics.logistic_system_name', $key)
                        ->whereBetween('car_date_logistics.date_at', [$date_1, $date_2]);
                });
        });
    }



    /**
     * НАЛИЧИЕ В ПЛАНЕ ПОСТАВЩИКА
     */
    public function hasPlan(Builder $builder, bool $value)
    {
        if($value)
            $builder->where('cars.disable_off', 1);
        else   
            $builder->where('cars.disable_off', 0); 
    }



    /**
     * COLOR
     */
    public function colors(Builder $builder, array $colors)
    {
        $builder->whereIn('dealer_colors.base_id', $colors);
    }



    public function search(Builder $builder, string $value)
    {
        $value = str_contains($value, '@') ? $value : '@'.$value;
        $value = explode('@', $value);
        list($column, $val) = $value;

        switch($column){
            case 'client':
                $builder->where(function($subQ) use ($val){
                    $subQ->where('clients.lastname', 'LIKE', '%'.$val.'%');
                    $subQ->orWhere('clients.company_name', 'LIKE', '%'.$val.'%');
                });
                break;
            case 'id':
                $builder->where('cars.id', $val);
                break;
            case 'vin':
                $builder->where('cars.vin', 'LIKE', '%' . $val . '%');
                break;
            case 'order':
                $builder->where('car_orders.order_number', 'LIKE', '%' . $val . '%');
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
