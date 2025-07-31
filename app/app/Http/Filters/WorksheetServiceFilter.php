<?php

namespace App\Http\Filters;

use App\Helpers\Date\DateHelper;
use App\Models\Worksheet\Service\WSMServiceCar;
use Illuminate\Database\Eloquent\Builder;

Class WorksheetServiceFilter extends AbstractFilter
{
    public const IDS = 'ids';
    public const INIT = 'init';
    public const SEARCH = 'search';
    public const DECORATOR = 'decorator';
    public const MANAGER = 'manager';
    public const CAR_TYPE = 'car_type';
    public const CATEGORY = 'category';
    public const SERVICE = 'service';
    public const SORT = 'sort';
    public const SALE_INTERVAL = 'sale_interval';
    public const WORKSHEET = 'worksheet_id';

    public function getCallbacks() : array
    {
        return [
            self::INIT          => [$this, 'init'],
            self::IDS           => [$this, 'ids'],
            self::SEARCH        => [$this, 'search'],
            self::DECORATOR     => [$this, 'decorator'],
            self::MANAGER       => [$this, 'manager'],
            self::CAR_TYPE      => [$this, 'car_type'],
            self::CATEGORY      => [$this, 'category'],
            self::SERVICE       => [$this, 'service'],
            self::SORT          => [$this, 'sort'],
            self::SALE_INTERVAL => [$this, 'sale_interval'],
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
            ->leftJoin('wsm_service_awards', 'wsm_service_awards.wsm_service_id', 'wsm_services.id')
            ->leftJoin('wsm_service_cars', 'wsm_service_cars.wsm_service_id', 'wsm_services.id')
            ->leftJoin('wsm_service_contracts', 'wsm_service_contracts.wsm_service_id', 'wsm_services.id')
            ->leftJoin('services', 'services.id', 'wsm_services.service_id')
            ->leftJoin('service_categories', 'service_categories.id', 'services.category_id')
            ->leftJoin('worksheets', 'worksheets.id', 'wsm_services.worksheet_id')
            ->leftJoin('clients', 'clients.id', 'worksheets.client_id')
            ->leftJoin('client_phones', 'client_phones.client_id', 'clients.id');
        
        $builder->groupBy('wsm_services.id');
    }



    public function worksheet(Builder $builder, int $value)
    {
        $builder->where('wsm_services.worksheet_id', $value);
    }



    public function ids(Builder $builder, array $values)
    {   
        $builder->whereIn('wsm_services.id', $values);
    }



    public function search(Builder $builder, string $value)
    {
        $builder->where(function($subQ) use ($value){
            $subQ->where('clients.lastname', 'LIKE', '%'.$value.'%');
            $subQ->orWhere('clients.company_name', 'LIKE', '%'.$value.'%');
            $subQ->orWhere('client_phones.phone', 'LIKE', '%'.$value.'%');
        });
    }



    public function decorator(Builder $builder, array $values)
    {
        $builder->whereIn('wsm_service_contracts.decorator_id', $values);
    }



    public function manager(Builder $builder, array $values)
    {
        $builder->whereIn('wsm_service_contracts.manager_id', $values);
    }



    public function car_type(Builder $builder, string $value)
    {
        if(in_array($value, WSMServiceCar::getArrayType()))
            $builder->where('wsm_service_cars.carable_type', WSMServiceCar::getModelName($value));
    }



    public function category(Builder $builder, array $values)
    {
        $builder->whereIn('services.category_id', $values);
    }



    public function service(Builder $builder, array $values)
    {
        $builder->whereIn('services.id', $values);
    }



    public function sort(Builder $builder, string $value)
    {
        $data = match($value){
            'calculate_asc'     => ['wsm_services.created_at', 'ASC'],
            'calculate_desc'    => ['wsm_services.created_at', 'DESC'],
            'begin_asc'         => ['wsm_service_contracts.begin_at', 'ASC'],
            'begin_desc'        => ['wsm_service_contracts.begin_at', 'DESC'],
            'register_asc'    => ['wsm_service_contracts.register_at', 'ASC'],
            'register_desc'   => ['wsm_service_contracts.register_at', 'DESC'],
            default             => ['wsm_services.id', 'DESC']
        };

        $builder->orderBy($data[0], $data[1]);
    }



    public function sale_interval(Builder $builder, array $values)
    {
        $dates = DateHelper::setDateToCarbon($values, 'd.m.Y');

        $builder->whereBetween('wsm_service_contracts.register_at', $dates);
    }
}