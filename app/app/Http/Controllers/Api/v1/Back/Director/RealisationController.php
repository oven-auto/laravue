<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RealisationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'intervals' => 'required|array',
            'intervals.*' => 'array',
            'intervals.*.0' => 'required',
            'salons' => 'sometimes|array'
        ]);

        $querySale = DB::table('wsm_reserve_sales')->select([
            DB::raw('COUNT(wsm_reserve_sales.id) as _count'),
            'marks.name as _mark',
            'marks.id as mark_id',
        ])
            ->leftJoin('wsm_reserve_new_cars', 'wsm_reserve_new_cars.id', 'wsm_reserve_sales.reserve_id')
            ->leftJoin('cars', 'cars.id', 'wsm_reserve_new_cars.car_id')
            ->leftJoin('marks', 'marks.id', 'cars.mark_id')
            ->groupBy('marks.id');

        $queryReport = DB::table('car_date_logistics')->select([
            DB::raw('COUNT(car_date_logistics.id) as _count'),
            'marks.name as _mark',
            'marks.id as mark_id',
        ])
            ->leftJoin('cars', 'cars.id', 'car_date_logistics.car_id')
            ->leftJoin('marks', 'marks.id', 'cars.mark_id')
            ->where('car_date_logistics.logistic_system_name', 'off_date')
            ->groupBy('marks.id');

        $res = DB::table('marks')->select([
            'marks.id',
            'marks.name as mark',
            'brands.name as brand',
            DB::raw('IFNULL(qSale._count, 0) as count_sale'),
            DB::raw('IFNULL(qReport._count, 0) as count_report'),
        ])
            ->leftJoinSub($querySale, 'qSale', 'qSale.mark_id', 'marks.id')
            ->leftJoinSub($queryReport, 'qReport', 'qReport.mark_id', 'marks.id')
            ->rightJoin('company_brands', 'company_brands.brand_id', 'marks.brand_id')
            ->leftJoin('brands', 'brands.id', 'marks.brand_id')
            ->where('marks.diller_status', 1)
            ->get();

        dd($res);
    }
}
