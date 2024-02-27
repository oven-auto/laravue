<?php

namespace App\Http\Filters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

Class WSMRedemptionCarFilter extends AbstractFilter
{
    private const AUTHOR_ID = 'author_id';
    private const IDS = 'ids';
    private const INIT = 'init';
    private const INTERVAL = 'interval';
    private const STATUS = 'status';
    private const INPUT = 'input';
    private const WORKSHEET_ID = 'worksheet_id';

    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';
        parent::__construct($queryParams);
    }

    protected function getCallbacks(): array
    {
        return [
            self::INIT               => [$this, 'init'],
            self::AUTHOR_ID          => [$this, 'authorIds'],
            self::IDS                => [$this, 'ids'],
            self::INTERVAL           => [$this, 'interval'],
            self::STATUS             => [$this, 'status'],
            self::INPUT              => [$this, 'input'],
            self::WORKSHEET_ID       => [$this, 'worksheetId'],
        ];
    }

    public function init(Builder $builder)
    {
        //$builder->select('wsm_redemption_cars.*');
        $builder->leftJoin('worksheets', 'worksheets.id', 'wsm_redemption_cars.worksheet_id');
        $builder->leftJoin('wsm_redemption_calculations', 'wsm_redemption_calculations.wsm_redemption_car_id', 'wsm_redemption_cars.id');
        $builder->leftJoin('wsm_redemption_offers',     'wsm_redemption_offers.wsm_redemption_car_id', 'wsm_redemption_cars.id');
        $builder->leftJoin('wsm_redemption_purchases', 'wsm_redemption_purchases.wsm_redemption_car_id', 'wsm_redemption_cars.id');

        $builder->groupBy('wsm_redemption_cars.id');
    }

    public function worksheetId(Builder $builder, $value)
    {
        $builder->where('wsm_redemption_cars.worksheet_id', $value);
    }

    public function authorIds(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->whereIn('wsm_redemption_cars.author_id', $value);
        elseif(is_numeric($value))
            $builder->where('wsm_redemption_cars.author_id', $value);
    }

    public function ids(Builder $builder, $value)
    {
        if(is_array($value))
            $builder->whereIn('wsm_redemption_cars.id', $value);
        elseif(is_numeric($value))
            $builder->where('wsm_redemption_cars.id', $value);
    }

    public function interval(Builder $builder, $value)
    {
        switch($value){
            case 'today':
                $builder->whereDate('wsm_redemption_cars.created_at', now());
                break;
            case 'week':
                $builder->whereBetween('wsm_redemption_cars.created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;
            case 'month':
                $builder->where(function($query)  {
                    $query
                        ->whereYear('wsm_redemption_cars.created_at', '=', now()->year)
                        ->whereMonth('wsm_redemption_cars.created_at', '=', now()->month);
                });
                break;
        }
    }

    public function status(Builder $builder, $value)
    {
        switch ($value) {
            case 'stock':
                $builder->where('wsm_redemption_cars.redemption_status_id', 2);
                break;
            case 'wait':
                $builder->where('wsm_redemption_cars.redemption_status_id', 1)
                    //->whereIn('worksheets.status_id', ['work', 'check'])
                    ->havingRaw(\DB::raw('COUNT(wsm_redemption_calculations.id) < 1'))
                    ->havingRaw(\DB::raw('COUNT(wsm_redemption_offers.id) < 1'))
                    ->havingRaw(\DB::raw('COUNT(wsm_redemption_purchases.id) < 1'));
                break;
            case 'work':
                $builder->where('wsm_redemption_cars.redemption_status_id', 1)
                    //->whereIn('worksheets.status_id', ['work', 'check'])
                    ->orHavingRaw(\DB::raw('COUNT(wsm_redemption_calculations.id) > 0'))
                    ->orHavingRaw(\DB::raw('COUNT(wsm_redemption_offers.id) > 0'))
                    ->orHavingRaw(\DB::raw('COUNT(wsm_redemption_purchases.id) > 0'));
                break;
            case 'close':
                $builder->where('wsm_redemption_cars.redemption_status_id', 3);
                break;
            case 'control':
                $builder->where('wsm_redemption_cars.redemption_status_id', 1)
                    ->whereIn('worksheets.status_id', ['confirm','check']);
                break;
        }
    }

    public function input(Builder $builder, $value)
    {
        $builder->leftJoin('client_cars', 'client_cars.id', 'wsm_redemption_cars.client_car_id');
        $builder->leftJoin('clients', 'clients.id', 'wsm_redemption_cars.client_id');
        $builder->leftJoin('client_phones', 'client_phones.client_id', 'clients.id');
        $builder->where(function($query) use ($value) {
            $query->where('wsm_redemption_cars.id', 'LIKE', '%'.$value.'%');
            $query->orWhere('client_cars.vin', 'LIKE', '%'.$value.'%');
            $query->orWhere('client_cars.register_plate', 'LIKE', '%'.$value.'%');
            $query->orWhere('client_phones.phone', 'LIKE', '%'.$value.'%');
            $query->orWhere('clients.lastname', 'LIKE', '%'.$value.'%');
        });
    }
}
