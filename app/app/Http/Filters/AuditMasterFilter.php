<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @OA\Schema(
 *   description = "Параметры фильтрации аудитов"
 * )
 */
Class AuditMasterFilter extends AbstractFilter
{
    /**
     * //TODO Доделать фильтры
     * Сделаны те что в ПДФ (
     * ids, 
     * authors, 
     * audits,
     * managers,
     * salons,
     * structures,
     * statuses,
     * create_interval,
     * )
     */

    public const INIT = 'init';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы мастер-аудитов", 
     * property="ids", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const IDS = 'ids';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы авторов/аудиторов.", 
     * property="authors", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const AUTHORS = 'authors';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы чек-листа.", 
     * property="audits", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const AUDITS = 'audits';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы менеджеров.", 
     * property="managers", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const MANAGERS = 'managers';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы салонов.", 
     * property="salons", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const SALONS = 'salons';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы подразделения.", 
     * property="structures", 
     * type="array", 
     * example="[1,2]", 
     * @OA\Items())
     * */
    public const STRUCTURES = 'structures';

    /**  @OA\Property(
     * format="array", 
     * description="Массив содержащий идентификаторы статусов.", 
     * property="statuses", 
     * type="array", 
     * example="['wait', 'arbitr', 'close']", 
     * @OA\Items())
     * */
    public const STATUSES = 'statuses';

    /**  @OA\Property(
     * format="string", 
     * description="Массив содержащий название интервала создания мастера-аудита (month,week,today,yesterday).", 
     * property="create_interval", 
     * type="string", 
     * example="today"
     * )
     * */
    public const CREATE_INTERVAL = 'create_interval';



    public function __construct(array $queryParams)
    {
        $queryParams['init'] = 'init';

        parent::__construct($queryParams);
    }



    public function getCallbacks() : array
    {
        return [
            self::INIT              => [$this, 'init'],
            
            self::IDS               => [$this, 'fnIds'],
            self::AUTHORS           => [$this, 'fnAuthors'],
            self::AUDITS            => [$this, 'fnAudits'],
            self::MANAGERS          => [$this, 'fnManagers'],
            self::SALONS            => [$this, 'fnSalons'],
            self::STRUCTURES        => [$this, 'fnStructures'],
            self::STATUSES          => [$this, 'fnStatuses'],
            self::CREATE_INTERVAL   => [$this, 'fnCreateInterval'],
        ];
    }



    public function init(Builder $builder,)
    {
        $builder
            ->leftJoin('trafics', 'trafics.id', 'audit_masters.trafic_id')
            ->leftJoin('audits', 'audits.id', 'audit_masters.audit_id')
            ->leftJoin('companies', 'companies.id', 'trafics.company_id')
            ->leftJoin('company_structures', 'company_structures.id', 'trafics.company_structure_id');
    }



    public function fnCreateInterval(Builder $builder, string $value)
    {
        $now = now();
        
        match($value){
            'month' => $builder->where(function($q) use($now){
                $q->whereYear('audit_masters.created_at', '=', $now->year)
                    ->whereMonth('audit_masters.created_at', '=', $now->month);
            }),
            'week' => $builder->whereBetween('audit_masters.created_at', [
                    $now->startOfWeek(), $now->endOfWeek()
                ]),
            'today' => $builder->whereDate('audit_masters.created_at', $now),
            'yesterday' => $builder->whereDate('audit_masters.created_at', $now),
            default => '',
        };
    }



    public function fnStatuses(Builder $builder, array $val)
    {
        $builder->whereIn('audit_masters.status', $val);
    }



    public function fnStructures(Builder $builder, array $val)
    {
        $builder->whereIn('company_structures.structure_id', $val);
    }



    public function salons(Builder $builder, array $val)
    {
        $builder->whereIn('companies.id', $val);
    }



    public function fnIds(Builder $builder, array $arr)
    {
        $builder->whereIn('audit_masters.id', $arr);
    }



    public function fnAuthors(Builder $builder, array $arr)
    {
        $builder->whereIn('audit_masters.author_id', $arr);
    }



    public function fnAudits(Builder $builder, array $arr)
    {
        $builder->whereIn('audits.id', $arr);
    }



    public function fnManagers(Builder $builder, array $arr)
    {
        $builder->where(function($q) use($arr){
            $q->whereIn('trafics.manager_id', $arr);
            $q->orWhereIn('audit_masters.author_id', $arr);
        });
    }
}