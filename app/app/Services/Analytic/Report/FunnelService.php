<?php

namespace App\Services\Analytic\Report;

use App\Models\Trafic;
use App\Services\Analytic\AbstractClasses\AbstractReport;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

Class FunnelService extends AbstractReport
{
    private $intervals;

    /**
     * Массив названий методов учавствующих в построении запроса
     * Все методы описаны ниже 
     * в обработчике для построения запроса цикл проходится по всем
     * значениям этого массива, и вызывает метод
     */
    private const FUN_NAMES = [
        'queryTrafic',
        'queryWorksheet',
        'queryReserve',
        'queryContract',
        'querySale',
        'queryReport'
    ];



    public function format($result)
    {
        $main = $result->where('type', 'trafic')->first();

        $result->each(function($item) use($main){
            if($item->type != 'trafic')
            {
                $arr = Arr::except((array) $item, ['type', 'name']);
                foreach($arr as $key => $param)
                {
                    $item->$key = [
                        'count' => $param,
                        'percent' => round($param/$main->$key*100,1),
                    ];
                }
            }
        });

        return $result;
    }



    public function handle(array $intervals, array $data = [])
    {
        $this->intervals = $this->convertInterval($intervals);

        $arr = array_map(function($funcName){
            return $this->makeSub(array_map(function($item) use($funcName){
                if(method_exists($this, $funcName))
                    return $this->$funcName($item,);
            }, $this->intervals));
        }, self::FUN_NAMES);
       
        $query = array_shift($arr);

        foreach($arr as $item)
            $query->unionAll($item);

        $res = $query->get();

        return $res;
    }



    public function makeSub(array $subQ)
    {
        $query = DB::table(array_shift($subQ), 'main')
            ->select([            
                DB::raw('main.type'),
                DB::raw('main.name'),
                DB::raw('main._count as count_main'),
            ]);
      
        foreach($subQ as $key => $subQ)
            $query
                ->addSelect([
                    DB::raw('sub_'.$key.'._count as count_'.$key),
                ])
                ->leftJoinSub($subQ, 'sub_'.$key, function($join) use($key){
                    $join->on('main.type', 'sub_'.$key.'.type');
                });

        return $query;
    }



    private function queryTrafic(array $data)
    {   
        $arr['company_id'] = 1;
        $arr['appeal_id'] = 1;

        $query = Trafic::select([
                DB::raw('count(trafics.id) as _count'),
                DB::raw('"trafic" as type'),
                DB::raw('"Обращения" as name'),
            ])
            ->leftJoin('trafic_clients', 'trafic_clients.trafic_id', 'trafics.id')
            ->leftJoin('companies', 'companies.id', 'trafics.company_id')
            ->leftJoin('trafic_appeals', 'trafic_appeals.id', 'trafics.trafic_appeal_id')
            ->leftJoin('appeals', 'appeals.id', 'trafic_appeals.appeal_id')
            ->onlyTarget()
            ->whereBetween('trafics.created_at', $data)
            ->where('companies.id', $arr['company_id'])
            ->where('appeals.id', $arr['appeal_id']);

        return $query;
    }



    private function queryWorksheet(array $data)
    {
        $arr['company_id'] = 1;
        $arr['appeal_id'] = 1;

        $query = DB::table('worksheets')->select([
                DB::raw('count(worksheets.id) as _count'),
                DB::raw('"worksheet" as type'),
                DB::raw('"Рабочие листы" as name'),
            ])
            ->whereBetween('worksheets.created_at', $data)
            ->where('worksheets.appeal_id', $arr['appeal_id'])
            ->where('worksheets.company_id', $arr['company_id']);

        return $query;
    }



    private function queryReserve(array $data)
    {
        $arr['company_id'] = 1;

        $query = DB::table('wsm_reserve_new_cars')->select([
                DB::raw('count(wsm_reserve_new_cars.id) as _count'),
                DB::raw('"reserve" as type'),
                DB::raw('"Резервы" as name'),
            ])
            ->leftJoin('worksheets', 'worksheets.id', 'wsm_reserve_new_cars.worksheet_id')
            ->whereBetween('wsm_reserve_new_cars.created_at', $data)
            ->where('worksheets.company_id', $arr['company_id']);

        return $query;
    }



    private function queryContract(array $data)
    {
        $arr['company_id'] = 1;

        $query = DB::table('wsm_reserve_new_car_contracts')->select([
                DB::raw('count(wsm_reserve_new_car_contracts.id) as _count'),
                DB::raw('"contract" as type'),
                DB::raw('"Контракты" as name'),
            ])
            ->leftJoin('wsm_reserve_new_cars', 'wsm_reserve_new_cars.id', 'wsm_reserve_new_car_contracts.reserve_id')
            ->leftJoin('worksheets', 'worksheets.id', 'wsm_reserve_new_cars.worksheet_id')
            ->whereBetween('wsm_reserve_new_car_contracts.created_at', $data)
            ->where('worksheets.company_id', $arr['company_id']);

        return $query;
    }



    private function querySale(array $data)
    {
        $arr['company_id'] = 1;
        
        $query = DB::table('wsm_reserve_sales')->select([
                DB::raw('count(wsm_reserve_sales.id) as _count'),
                DB::raw('"sale" as type'),
                DB::raw('"Продажи" as name'),
            ])
            ->leftJoin('wsm_reserve_new_cars', 'wsm_reserve_new_cars.id', 'wsm_reserve_sales.reserve_id')
            ->leftJoin('worksheets', 'worksheets.id', 'wsm_reserve_new_cars.worksheet_id')
            ->whereBetween('wsm_reserve_sales.created_at', $data)            
            ->where('worksheets.company_id', $arr['company_id']);

        return $query;
    }
}