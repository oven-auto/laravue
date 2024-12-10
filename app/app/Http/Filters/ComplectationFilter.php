<?php

namespace App\Http\Filters;

use App\Models\Car;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ComplectationFilter extends AbstractFilter
{
    public const BRAND_ID   = 'brand_id';
    public const NAME       = 'name';
    public const CODE       = 'code';
    public const MARK_ID    = 'mark_id';
    public const STATUS     = 'status';
    public const IDS        = 'ids';
    public const TRASH      = 'trash';
    public const INSALE     = 'insale';
    public const ACTION     = 'action';
    public const INPUT      = 'input';

    public const INIT = 'init';

    protected function getCallbacks(): array
    {
        return [
            self::IDS               => [$this, 'ids'],
            self::INIT              => [$this, 'init'],
            self::BRAND_ID          => [$this, 'brandId'],
            self::NAME              => [$this, 'name'],
            self::CODE              => [$this, 'code'],
            self::MARK_ID           => [$this, 'markId'],
            self::TRASH             => [$this, 'trash'],
            self::STATUS            => [$this, 'status'],
            self::INSALE            => [$this, 'inSale'],
            self::ACTION            => [$this, 'action'],
            self::INPUT             => [$this, 'input'],
        ];
    }



    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';
        $queryParams['status'] = $queryParams['status'] ?? 0;
        parent::__construct($queryParams);
    }



    public function init(Builder $builder)
    {
        $builder->leftJoin('marks', 'marks.id', 'complectations.mark_id');

        $builder
            ->saledCountCars()
            ->activeCountCars();

        $builder->leftJoin('complectation_current_prices', 'complectation_current_prices.complectation_id', 'complectations.id');

        $builder->groupBy('complectations.id');
    }



    /**
     * Получить те которые подходят под поиск из input
     */
    public function input(Builder $builder, string $value)
    {
        $builder->where(function($query) use($value){
            $query->where('complectations.code', 'LIKE', '%'.$value.'%');
            $query->orWhere('complectations.name', 'LIKE', '%'.$value.'%');
        });
    }



    /**
     * Получить только те которые нужно проверить
     */
    public function action(Builder $builder, $value)
    {
        match ($value){
            'totrash'   => $builder->withoutTrashed()->havingRaw(DB::raw('max(active_car) < 1')),
            'towork'    => $builder->onlyTrashed()->havingRaw(DB::raw('max(active_car) > 0')),
            'tochange'  => $builder
                            ->havingRaw(DB::raw('max(active_car) > 0'))
                            ->withoutTrashed()
                            ->where('complectation_current_prices.begin_at', '<', DB::raw('(SELECT max(begin_at) FROM complectation_prices)')),
            default => '',
        };
    }



    /**
     * Получить только те комплектации, которые: 
     * 1 - имеют хотя бы одну проданную машину <sold>
     * 2 - имеют хотя бы одну продающуюся машину <sale>
     */
    public function inSale(Builder $builder, string $value)
    {
        match($value) {
            'sold' => $builder->havingRaw(DB::raw('max(saled_cars) > 0')),
            'sale' => $builder->havingRaw(DB::raw('max(active_car) > 0')),
            default => ''
        };
    }



    /**
     * Показать только те комплектации, которые:
     * 1 - в архиве <trash>
     * 2 - активные <active>
     * 3 - все <all|empty>
     */
    public function status(Builder $builder, string $value)
    {
        match($value){
            'all' => $builder->withTrashed(),
            'trash' => $builder->onlyTrashed(),
            'active' => $builder->withoutTrashed(),
            default => $builder->withTrashed()
        };
    }



    /**
     * Показать только удаленные комплектции
     */
    public function trash(Builder $builder, int|string $value)
    {
        $builder->onlyTrashed();
    }



    /**
     * Показать только те комплектации, которые имеют указанные ID
     */
    public function ids(Builder $builder, array $data)
    {
        $builder->whereIn('complectations.id', $data);
    }



    /**
     * Показать только те комплектации определенного бренда
     */
    public function brandId(Builder $builder, $value)
    {
        $builder->whereIn('marks.brand_id',  $value);
    }



    /**
     * Показать только те комплектации определенной модели
     */
    public function markId(Builder $builder, $value)
    {
        $builder->whereIn('marks.id',  $value);
    }



    /**
     * Показать только те комплектации, которые в реквезите CODE содержат строку
     */
    public function code(Builder $builder, $value)
    {
        $builder->where('complectations.code', 'like', '%'. $value.'%');
    }



    /**
     * Показать только те комплектации, которые в реквезите NAME содержат строку
     */
    public function name(Builder $builder, $value)
    {
        $builder->where('complectations.name', 'like', '%'. $value.'%');
    }
}
